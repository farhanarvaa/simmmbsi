<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    /**
     * Menampilkan daftar mahasiswa magang. Khusus Admin/Mentor.
     */
    public function index(): View
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus Admin/Mentor.');
        }

        // Hanya tampilkan pendaftar yang sudah mengajukan berkas (tidak termasuk akun yang baru registrasi/belum mengajukan)
        $mahasiswas = Mahasiswa::where('status_pendaftaran', '!=', 'belum_daftar')
            ->latest()
            ->paginate(10);

        return view('mahasiswas.index', compact('mahasiswas'));
    }

    /**
     * Menampilkan form tambah mahasiswa.
     */
    public function create(): View
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus Admin/Mentor.');
        }

        return view('mahasiswas.create', ['cabangs' => Cabang::orderBy('nama')->get()]);
    }

    /**
     * Menyimpan data mahasiswa baru oleh Admin.
     */
    public function store(Request $request): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus Admin/Mentor.');
        }

        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:255', 'unique:mahasiswas,nim'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'universitas' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', 'max:255'],
            'id_cabang' => ['nullable', 'exists:cabang,id'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:aktif,selesai,dibatalkan'],
            'status_pendaftaran' => ['nullable', 'in:diajukan,revisi,ditolak,diterima,ditempatkan'],
        ]);

        if (!isset($validated['status_pendaftaran'])) {
            $validated['status_pendaftaran'] = !empty($validated['id_cabang']) ? 'ditempatkan' : 'diterima';
        }

        Mahasiswa::create($validated);

        return redirect()->route('mahasiswas.index')->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    /**
     * Entry point saat mahasiswa mengklik "Ajukan Pendaftaran Magang".
     * Memeriksa kelengkapan profil akademik terlebih dahulu:
     * - Jika belum lengkap: arahkan ke form "Lengkapi Profil"
     * - Jika sudah lengkap: arahkan ke form upload berkas persyaratan
     */
    public function ajukanPendaftaran(): RedirectResponse
    {
        $user = Auth::user();
        if ($user->role !== 'mahasiswa') {
            abort(403, 'Akses khusus mahasiswa.');
        }

        $mahasiswa = Mahasiswa::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama_lengkap' => $user->name,
                'no_hp' => $user->no_hp,
                'status_pendaftaran' => 'belum_daftar',
                'status' => 'aktif',
            ]
        );

        // Jika sudah diterima / ditempatkan
        if (in_array($mahasiswa->status_pendaftaran, ['diterima', 'ditempatkan'])) {
            return redirect()->route('mahasiswa.dashboard');
        }

        // 4. Cek dulu kelengkapan profil akademik (NIM, universitas, fakultas, prodi, semester)
        if (!$mahasiswa->isProfilAkademikLengkap()) {
            return redirect()->route('mahasiswa.profil.lengkapi')
                ->with('warning', 'Profil akademik Anda belum lengkap. Silakan lengkapi NIM, Perguruan Tinggi, Fakultas, Program Studi, dan Semester terlebih dahulu sebelum melanjutkan pengajuan pendaftaran magang.');
        }

        // 5. Setelah profil lengkap, lanjutkan ke form upload berkas
        return redirect()->route('mahasiswa.pendaftaran.berkas');
    }

    /**
     * Form Lengkapi Profil Akademik Mahasiswa
     */
    public function formLengkapiProfil(): View|RedirectResponse
    {
        $user = Auth::user();
        if ($user->role !== 'mahasiswa') {
            abort(403, 'Akses khusus mahasiswa.');
        }

        $mahasiswa = Mahasiswa::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama_lengkap' => $user->name,
                'no_hp' => $user->no_hp,
                'status_pendaftaran' => 'belum_daftar',
                'status' => 'aktif',
            ]
        );

        return view('pages.lengkapi-profil', compact('user', 'mahasiswa'));
    }

    /**
     * Menyimpan kelengkapan data profil akademik mahasiswa
     */
    public function updateLengkapiProfil(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user->role !== 'mahasiswa') {
            abort(403, 'Akses khusus mahasiswa.');
        }

        $mahasiswa = Mahasiswa::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama_lengkap' => $user->name,
                'no_hp' => $user->no_hp,
                'status_pendaftaran' => 'belum_daftar',
                'status' => 'aktif',
            ]
        );

        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:255', Rule::unique('mahasiswas', 'nim')->ignore($mahasiswa->id)],
            'universitas' => ['required', 'string', 'max:255'],
            'fakultas' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'string', 'max:20'],
            'no_hp' => ['required', 'string', 'max:30'],
        ], [
            'nim.required' => 'Nomor Induk Mahasiswa (NIM) wajib diisi.',
            'nim.unique' => 'NIM ini sudah terdaftar pada akun mahasiswa lain.',
            'universitas.required' => 'Perguruan Tinggi / Universitas wajib diisi.',
            'fakultas.required' => 'Fakultas wajib diisi.',
            'jurusan.required' => 'Jurusan / Program Studi wajib diisi.',
            'semester.required' => 'Semester saat ini wajib diisi.',
            'no_hp.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        $mahasiswa->update([
            'nim' => $validated['nim'],
            'universitas' => $validated['universitas'],
            'fakultas' => $validated['fakultas'],
            'jurusan' => $validated['jurusan'],
            'semester' => $validated['semester'],
            'no_hp' => $validated['no_hp'],
            'nama_lengkap' => $user->name,
        ]);

        $user->update([
            'no_hp' => $validated['no_hp'],
        ]);

        return redirect()->route('mahasiswa.pendaftaran.berkas')
            ->with('success', 'Profil akademik berhasil disimpan! Silakan lanjutkan ke tahap akhir: unggah berkas persyaratan magang.');
    }

    /**
     * Form Unggah Berkas Persyaratan Magang (Tahap Akhir Pengajuan)
     */
    public function formUploadBerkas(): View|RedirectResponse
    {
        $user = Auth::user();
        if ($user->role !== 'mahasiswa') {
            abort(403, 'Akses khusus mahasiswa.');
        }

        $mahasiswa = Mahasiswa::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama_lengkap' => $user->name,
                'no_hp' => $user->no_hp,
                'status_pendaftaran' => 'belum_daftar',
                'status' => 'aktif',
            ]
        );

        // Jika profil akademik belum lengkap, paksa kembali ke form lengkapi profil
        if (!$mahasiswa->isProfilAkademikLengkap()) {
            return redirect()->route('mahasiswa.profil.lengkapi')
                ->with('warning', 'Silakan lengkapi profil akademik Anda terlebih dahulu sebelum mengunggah berkas persyaratan.');
        }

        // Jika sudah diterima / ditempatkan
        if (in_array($mahasiswa->status_pendaftaran, ['diterima', 'ditempatkan'])) {
            return redirect()->route('mahasiswa.dashboard');
        }

        return view('pages.upload-berkas', compact('user', 'mahasiswa'));
    }

    /**
     * Menyimpan atau mengupdate formulir berkas pendaftaran magang dari mahasiswa.
     * Setelah profil + berkas lengkap, status berubah jadi "diajukan" dan masuk antrian verifikasi Admin/Mentor.
     */
    public function storePendaftaran(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->role !== 'mahasiswa') {
            abort(403, 'Akses khusus mahasiswa.');
        }

        $mahasiswa = Mahasiswa::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama_lengkap' => $user->name,
                'no_hp' => $user->no_hp,
                'status_pendaftaran' => 'belum_daftar',
                'status' => 'aktif',
            ]
        );

        // Blokir jika status pendaftaran sudah melewati tahap 'diajukan' atau 'revisi'
        $statusTerkunci = ['diterima', 'ditempatkan'];
        if (in_array($mahasiswa->status_pendaftaran, $statusTerkunci)) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Pendaftaran Anda sudah diproses (status: ' . $mahasiswa->status_pendaftaran . '). Formulir pendaftaran tidak dapat diubah lagi.');
        }

        // Cek kelengkapan profil akademik
        if (!$mahasiswa->isProfilAkademikLengkap()) {
            return redirect()->route('mahasiswa.profil.lengkapi')
                ->with('warning', 'Profil akademik Anda belum lengkap. Silakan lengkapi data profil terlebih dahulu.');
        }

        // Dokumen proposal magang & surat pengantar wajib diisi untuk pendaftaran baru
        $proposalRule = ($mahasiswa->proposal_magang)
            ? ['nullable', 'file', 'mimes:pdf', 'max:5120']
            : ['required', 'file', 'mimes:pdf', 'max:5120'];

        $suratPengantarRule = ($mahasiswa->surat_pengantar)
            ? ['nullable', 'file', 'mimes:pdf', 'max:5120']
            : ['required', 'file', 'mimes:pdf', 'max:5120'];

        $validated = $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'proposal_magang' => $proposalRule,
            'surat_pengantar' => $suratPengantarRule,
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'transkrip' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ], [
            'proposal_magang.required' => 'Dokumen Proposal Magang (format PDF) wajib diunggah.',
            'surat_pengantar.required' => 'Surat Pengantar Kampus (format PDF) wajib diunggah.',
            'proposal_magang.mimes' => 'Format file proposal magang harus berupa dokumen PDF.',
            'surat_pengantar.mimes' => 'Format file surat pengantar kampus harus berupa dokumen PDF.',
            'cv.mimes' => 'Format file CV harus berupa dokumen PDF.',
            'transkrip.mimes' => 'Format file transkrip nilai harus berupa dokumen PDF.',
            'proposal_magang.max' => 'Ukuran file proposal magang maksimal 5 MB.',
            'surat_pengantar.max' => 'Ukuran file surat pengantar maksimal 5 MB.',
            'cv.max' => 'Ukuran file CV maksimal 5 MB.',
            'transkrip.max' => 'Ukuran file transkrip nilai maksimal 5 MB.',
        ]);

        $data = [
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? $mahasiswa->tanggal_mulai,
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? $mahasiswa->tanggal_selesai,
            'status_pendaftaran' => 'diajukan', // 6. Berubah jadi "Diajukan" dan masuk ke antrean verifikasi
            'catatan_revisi' => null,
        ];

        $uploadedPaths = [];

        try {
            DB::transaction(function () use ($request, &$data, &$uploadedPaths, $mahasiswa) {
                // Handle upload file
                foreach (['proposal_magang', 'surat_pengantar', 'cv', 'transkrip'] as $fileKey) {
                    if ($request->hasFile($fileKey)) {
                        $file = $request->file($fileKey);
                        $filename = time() . '_' . $fileKey . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('dokumen_magang', $filename, 'public');
                        $data[$fileKey] = $path;
                        $uploadedPaths[] = $path;
                    }
                }

                $mahasiswa->update($data);
            });
        } catch (\Throwable $e) {
            // Hapus file yang terlanjur di-store jika DB gagal
            foreach ($uploadedPaths as $p) {
                if (Storage::disk('public')->exists($p)) {
                    Storage::disk('public')->delete($p);
                }
            }
            throw $e;
        }

        return redirect()->route('mahasiswa.dashboard')->with('success', 'Berkas pendaftaran magang berhasil diajukan dan masuk ke antrean verifikasi Admin/Mentor BSI Cabang.');
    }

    /**
     * Keputusan Admin Cabang untuk pendaftaran (Terima / Minta Revisi / Tolak / Penempatan).
     */
    public function updateStatusPendaftaran(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus Admin/Mentor.');
        }

        $validated = $request->validate([
            'status_pendaftaran' => ['required', 'in:diajukan,revisi,ditolak,diterima,ditempatkan'],
            'catatan_revisi' => ['nullable', 'string'],
            'id_cabang' => ['nullable', 'exists:cabang,id'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        if ($validated['status_pendaftaran'] === 'ditempatkan' || (!empty($validated['id_cabang']) && $validated['status_pendaftaran'] === 'diterima')) {
            $validated['status_pendaftaran'] = 'ditempatkan';
            $validated['status'] = 'aktif';
        } elseif ($validated['status_pendaftaran'] === 'ditolak') {
            $validated['status'] = 'dibatalkan';
        }

        $mahasiswa->update($validated);

        return redirect()->back()->with('success', 'Status pendaftaran mahasiswa berhasil diperbarui.');
    }

    /**
     * Pencatatan administratif pelaporan ke RO Pusat (non-blocking).
     */
    public function laporRO(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus Admin/Mentor.');
        }

        $validated = $request->validate([
            'nomor_referensi_laporan_ro' => ['required', 'string', 'max:255'],
            'tanggal_pelaporan_ro' => ['required', 'date'],
        ]);

        $validated['status_pelaporan_ro'] = 'sudah_dilaporkan';

        $mahasiswa->update($validated);

        return redirect()->back()->with('success', 'Pencatatan pelaporan ke RO Pusat berhasil disimpan.');
    }

    /**
     * Menampilkan detail mahasiswa.
     */
    public function show(Mahasiswa $mahasiswa): View
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus Admin/Mentor.');
        }

        return view('mahasiswas.show', compact('mahasiswa'));
    }

    /**
     * Menampilkan form edit mahasiswa.
     */
    public function edit(Mahasiswa $mahasiswa): View
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus Admin/Mentor.');
        }

        return view('mahasiswas.create', [
            'mahasiswa' => $mahasiswa,
            'cabangs' => Cabang::orderBy('nama')->get(),
        ]);
    }

    /**
     * Mengupdate data mahasiswa.
     */
    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus Admin/Mentor.');
        }

        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:255', 'unique:mahasiswas,nim,' . $mahasiswa->id],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'universitas' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', 'max:255'],
            'id_cabang' => ['nullable', 'exists:cabang,id'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:aktif,selesai,dibatalkan'],
            'status_pendaftaran' => ['nullable', 'in:diajukan,revisi,ditolak,diterima,ditempatkan'],
        ]);

        $mahasiswa->update($validated);

        return redirect()->route('mahasiswas.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    /**
     * Menghapus data mahasiswa.
     */
    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus Admin/Mentor.');
        }

        $mahasiswa->delete();

        return redirect()->route('mahasiswas.index')->with('success', 'Data mahasiswa berhasil dihapus.');
    }
}
