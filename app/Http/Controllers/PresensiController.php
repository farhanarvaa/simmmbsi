<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PresensiController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        // Blokir mahasiswa yang belum ditempatkan
        if ($user->role === 'mahasiswa') {
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            if (!$mahasiswa || ($mahasiswa->status_pendaftaran !== 'ditempatkan' && $mahasiswa->status !== 'selesai')) {
                return redirect()->route('mahasiswa.dashboard')->with('error', 'Anda belum dapat mengakses presensi. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.');
            }
        }

        $presensis = $user->role === 'admin'
            ? Presensi::with('user')->latest('tanggal')->latest()->paginate(10)
            : Presensi::where('user_id', $user->id)->latest('tanggal')->latest()->paginate(10);

        // Presensi hari ini (untuk deteksi mode Datang/Pulang di modal)
        $presensiHariIni = $user->role !== 'admin'
            ? Presensi::where('user_id', $user->id)->whereDate('tanggal', now()->toDateString())->first()
            : null;

        return view('presensis.index', compact('presensis', 'presensiHariIni'));
    }

    public function create(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('presensis.index')->with('error', 'Admin/Mentor tidak perlu mengisi presensi.');
        }

        // Blokir jika mahasiswa belum ditempatkan
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        if (!$mahasiswa || ($mahasiswa->status_pendaftaran !== 'ditempatkan' && $mahasiswa->status !== 'selesai')) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Anda belum dapat mengakses presensi. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.');
        }

        if ($mahasiswa->status === 'selesai') {
            return redirect()->route('presensis.index')->with('error', 'Program magang Anda telah selesai. Halaman presensi kini hanya tersedia dalam mode riwayat (hanya baca).');
        }

        return view('presensis.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Admin/Mentor tidak diizinkan membuat data presensi.'], 403);
            }
            abort(403, 'Admin/Mentor tidak diizinkan membuat data presensi.');
        }

        // Blokir jika mahasiswa belum ditempatkan
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        if (!$mahasiswa || ($mahasiswa->status_pendaftaran !== 'ditempatkan' && $mahasiswa->status !== 'selesai')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Anda belum dapat mengakses presensi. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.'], 403);
            }
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Anda belum dapat mengakses presensi. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.');
        }

        // Blokir jika status magang sudah selesai
        if ($mahasiswa->status === 'selesai') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Program magang Anda telah selesai. Anda tidak dapat menambah presensi baru.'], 403);
            }
            return redirect()->route('presensis.index')->with('error', 'Program magang Anda telah selesai. Anda tidak dapat menambah presensi baru.');
        }

        // Cek presensi hari ini
        $presensiHariIni = Presensi::where('user_id', $user->id)
            ->whereDate('tanggal', $request->input('tanggal'))
            ->first();

        $jenis = $request->input('jenis_presensi', 'datang'); // 'datang' atau 'pulang'

        // Jika memilih jenis pulang tapi belum ada presensi datang
        if ($jenis === 'pulang' && !$presensiHariIni) {
            $msg = 'Anda belum mengisi Presensi Masuk untuk tanggal ini. Silakan lakukan Presensi Masuk terlebih dahulu.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg, 'errors' => ['jenis_presensi' => [$msg]]], 422);
            }
            return back()->withInput()->withErrors(['jenis_presensi' => $msg]);
        }

        // ── Mode PULANG: update jam_pulang pada record yang sudah ada ──
        if ($presensiHariIni) {
            $formattedDate = Carbon::parse($request->input('tanggal'))->format('d-m-Y');

            // Sudah lengkap (ada jam_masuk & jam_pulang) → tolak
            if ($presensiHariIni->jam_masuk && $presensiHariIni->jam_pulang) {
                $msg = "Presensi pada tanggal {$formattedDate} sudah lengkap (jam masuk & pulang sudah terisi).";
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['message' => $msg, 'errors' => ['tanggal' => [$msg]]], 422);
                }
                return back()->withInput()->withErrors(['tanggal' => $msg]);
            }

            // Ada jam_masuk tapi belum ada jam_pulang → update jam_pulang
            if ($presensiHariIni->jam_masuk && !$presensiHariIni->jam_pulang) {
                $validated = $request->validate([
                    'jam_pulang' => ['required', 'date_format:H:i'],
                ], [
                    'jam_pulang.required' => 'Jam pulang wajib diisi untuk presensi pulang.',
                    'jam_pulang.date_format' => 'Format jam pulang tidak valid.',
                ]);

                $jamMasukTime = Carbon::parse($presensiHariIni->jam_masuk)->format('H:i');
                if ($validated['jam_pulang'] < $jamMasukTime) {
                    $msg = "Jam pulang ({$validated['jam_pulang']}) tidak boleh lebih awal dari jam masuk ({$jamMasukTime}).";
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['message' => $msg, 'errors' => ['jam_pulang' => [$msg]]], 422);
                    }
                    return back()->withInput()->withErrors(['jam_pulang' => $msg]);
                }

                $presensiHariIni->update(['jam_pulang' => $validated['jam_pulang']]);
                $presensiHariIni->load('user');

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success'          => true,
                        'is_pulang_update' => true,
                        'message'          => 'Jam pulang berhasil dicatat.',
                        'data' => [
                            'id'               => $presensiHariIni->id,
                            'tanggal'          => $presensiHariIni->tanggal,
                            'tanggal_formatted'=> Carbon::parse($presensiHariIni->tanggal)->format('d-m-Y'),
                            'status'           => $presensiHariIni->status,
                            'status_upper'     => strtoupper($presensiHariIni->status),
                            'jam_masuk'        => $presensiHariIni->jam_masuk ? Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') : '-',
                            'jam_pulang'       => $presensiHariIni->jam_pulang ? Carbon::parse($presensiHariIni->jam_pulang)->format('H:i') : '-',
                            'lokasi'           => $presensiHariIni->lokasi ?: '-',
                            'catatan'          => $presensiHariIni->catatan ?: '-',
                            'bukti_path'       => $presensiHariIni->bukti_path,
                            'bukti_url'        => $presensiHariIni->bukti_path ? asset('storage/' . $presensiHariIni->bukti_path) : null,
                            'user_name'        => $presensiHariIni->user->name ?? $user->name,
                            'edit_url'         => route('presensis.edit', $presensiHariIni),
                            'destroy_url'      => route('presensis.destroy', $presensiHariIni),
                        ]
                    ]);
                }
                return redirect()->route('presensis.index')->with('success', 'Jam pulang berhasil dicatat.');
            }

            // Presensi sudah ada tapi jenis izin/sakit (tidak ada jam) → tolak double
            $msg = "Anda sudah mengisi presensi pada tanggal {$formattedDate}.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg, 'errors' => ['tanggal' => [$msg]]], 422);
            }
            return back()->withInput()->withErrors(['tanggal' => $msg]);
        }

        // ── Mode DATANG: buat record presensi baru ──
        // Validasi: Tanggal presensi harus dalam rentang periode magang
        $tanggalInput = Carbon::parse($request->input('tanggal'));
        if ($mahasiswa->tanggal_mulai && $tanggalInput->lt(Carbon::parse($mahasiswa->tanggal_mulai))) {
            $msg = 'Tanggal presensi tidak boleh sebelum tanggal mulai magang (' . Carbon::parse($mahasiswa->tanggal_mulai)->format('d-m-Y') . ').';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg, 'errors' => ['tanggal' => [$msg]]], 422);
            }
            return back()->withInput()->withErrors(['tanggal' => $msg]);
        }
        if ($mahasiswa->tanggal_selesai && $tanggalInput->gt(Carbon::parse($mahasiswa->tanggal_selesai))) {
            $msg = 'Tanggal presensi tidak boleh setelah tanggal selesai magang (' . Carbon::parse($mahasiswa->tanggal_selesai)->format('d-m-Y') . ').';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg, 'errors' => ['tanggal' => [$msg]]], 422);
            }
            return back()->withInput()->withErrors(['tanggal' => $msg]);
        }

        // Validasi input presensi harian
        $validated = $request->validate([
            'tanggal'    => ['required', 'date'],
            'status'     => ['required', 'in:hadir,izin,sakit'],
            'jam_masuk'  => ['required_if:status,hadir', 'nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i', 'after:jam_masuk'],
            'lokasi'     => ['required_if:status,hadir', 'nullable', 'string', 'max:255'],
            'catatan'    => ['required_if:status,izin,sakit', 'nullable', 'string'],
            'bukti'      => ['required_if:status,hadir,izin,sakit', 'nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'tanggal.required'    => 'Tanggal presensi wajib diisi.',
            'status.required'     => 'Status kehadiran wajib dipilih.',
            'jam_masuk.required_if' => 'Jam masuk wajib diisi untuk status Hadir.',
            'jam_pulang.after'    => 'Jam pulang harus lebih akhir daripada jam masuk.',
            'lokasi.required_if'  => 'Lokasi kegiatan presensi wajib diisi untuk status Hadir.',
            'catatan.required_if' => 'Catatan keterangan/alasan wajib diisi untuk status Izin atau Sakit.',
            'bukti.required_if'   => 'Foto bukti presensi (berisi waktu & lokasi) wajib diunggah.',
            'bukti.image'         => 'File bukti presensi harus berupa gambar (JPG, JPEG, PNG, atau WEBP).',
            'bukti.mimes'         => 'Format file foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'bukti.max'           => 'Ukuran foto bukti presensi maksimal 5MB.',
        ]);

        $data = [
            'user_id'    => $user->id,
            'tanggal'    => $validated['tanggal'],
            'status'     => $validated['status'],
            'jam_masuk'  => $validated['jam_masuk'] ?? null,
            'jam_pulang' => $validated['jam_pulang'] ?? null,
            'lokasi'     => $validated['lokasi'] ?? null,
            'catatan'    => $validated['catatan'] ?? null,
        ];

        // Handle upload file bukti presensi
        if ($request->hasFile('bukti')) {
            $file     = $request->file('bukti');
            $filename = time() . '_presensi_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('bukti_presensi', $filename, 'public');
            $data['bukti_path'] = $path;
        }

        $presensi = Presensi::create($data);
        $presensi->load('user');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'          => true,
                'is_pulang_update' => false,
                'message'          => 'Presensi harian berhasil disimpan.',
                'data' => [
                    'id'               => $presensi->id,
                    'tanggal'          => $presensi->tanggal,
                    'tanggal_formatted'=> Carbon::parse($presensi->tanggal)->format('d-m-Y'),
                    'status'           => $presensi->status,
                    'status_upper'     => strtoupper($presensi->status),
                    'jam_masuk'        => $presensi->jam_masuk ? Carbon::parse($presensi->jam_masuk)->format('H:i') : '-',
                    'jam_pulang'       => $presensi->jam_pulang ? Carbon::parse($presensi->jam_pulang)->format('H:i') : '-',
                    'lokasi'           => $presensi->lokasi ?: '-',
                    'catatan'          => $presensi->catatan ?: '-',
                    'bukti_path'       => $presensi->bukti_path,
                    'bukti_url'        => $presensi->bukti_path ? asset('storage/' . $presensi->bukti_path) : null,
                    'user_name'        => $presensi->user->name ?? $user->name,
                    'edit_url'         => route('presensis.edit', $presensi),
                    'destroy_url'      => route('presensis.destroy', $presensi),
                ]
            ]);
        }

        return redirect()->route('presensis.index')->with('success', 'Presensi harian berhasil disimpan.');
    }

    public function show(Presensi $presensi): RedirectResponse
    {
        return redirect()->route('presensis.edit', $presensi);
    }

    public function edit(Presensi $presensi): View|RedirectResponse
    {
        $this->authorizeRecord($presensi);

        $user = Auth::user();
        if ($user->role === 'mahasiswa') {
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            if ($mahasiswa && $mahasiswa->status === 'selesai') {
                return redirect()->route('presensis.index')->with('error', 'Program magang Anda telah selesai. Data presensi bersifat read-only.');
            }
        }

        return view('presensis.create', compact('presensi'));
    }

    public function update(Request $request, Presensi $presensi): RedirectResponse
    {
        $this->authorizeRecord($presensi);

        $user = Auth::user();
        if ($user->role === 'mahasiswa') {
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            if ($mahasiswa && $mahasiswa->status === 'selesai') {
                return redirect()->route('presensis.index')->with('error', 'Program magang Anda telah selesai. Data presensi bersifat read-only.');
            }
        }

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'in:hadir,izin,sakit'],
            'jam_masuk' => ['required_if:status,hadir', 'nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i', 'after:jam_masuk'],
            'lokasi' => ['required_if:status,hadir', 'nullable', 'string', 'max:255'],
            'catatan' => ['required_if:status,izin,sakit', 'nullable', 'string'],
            'bukti' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'tanggal.required' => 'Tanggal presensi wajib diisi.',
            'status.required' => 'Status kehadiran wajib dipilih.',
            'jam_masuk.required_if' => 'Jam masuk wajib diisi untuk status Hadir.',
            'jam_pulang.after' => 'Jam pulang harus lebih akhir daripada jam masuk.',
            'lokasi.required_if' => 'Lokasi kegiatan presensi wajib diisi untuk status Hadir.',
            'catatan.required_if' => 'Catatan keterangan/alasan wajib diisi untuk status Izin atau Sakit.',
            'bukti.image' => 'File bukti presensi harus berupa gambar (JPG, JPEG, PNG, atau WEBP).',
            'bukti.mimes' => 'Format file foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'bukti.max' => 'Ukuran foto bukti presensi maksimal 5MB.',
        ]);

        $data = [
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'],
            'jam_masuk' => $validated['jam_masuk'] ?? null,
            'jam_pulang' => $validated['jam_pulang'] ?? null,
            'lokasi' => $validated['lokasi'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
        ];

        // Handle upload file bukti jika diganti
        if ($request->hasFile('bukti')) {
            if ($presensi->bukti_path && Storage::disk('public')->exists($presensi->bukti_path)) {
                Storage::disk('public')->delete($presensi->bukti_path);
            }
            $file = $request->file('bukti');
            $filename = time() . '_presensi_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('bukti_presensi', $filename, 'public');
            $data['bukti_path'] = $path;
        }

        $presensi->update($data);

        return redirect()->route('presensis.index')->with('success', 'Presensi berhasil diperbarui.');
    }

    public function destroy(Presensi $presensi): RedirectResponse
    {
        $user = Auth::user();

        // Admin tidak diizinkan menghapus data presensi mahasiswa (data historis)
        if ($user->role === 'admin') {
            return redirect()->route('presensis.index')->with('error', 'Admin tidak diizinkan menghapus data presensi mahasiswa.');
        }

        $this->authorizeRecord($presensi);

        if ($user->role === 'mahasiswa') {
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            if ($mahasiswa && $mahasiswa->status === 'selesai') {
                return redirect()->route('presensis.index')->with('error', 'Program magang Anda telah selesai. Data presensi bersifat read-only.');
            }
        }

        if ($presensi->bukti_path && Storage::disk('public')->exists($presensi->bukti_path)) {
            Storage::disk('public')->delete($presensi->bukti_path);
        }

        $presensi->delete();

        return redirect()->route('presensis.index')->with('success', 'Presensi berhasil dihapus.');
    }

    private function authorizeRecord(Presensi $presensi): void
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $presensi->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }
    }
}
