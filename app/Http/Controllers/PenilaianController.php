<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PenilaianController extends Controller
{
    /**
     * Halaman penilaian.
     * Admin : melihat seluruh mahasiswa (semua status) + rekap penilaian.
     * Mahasiswa : melihat nilai milik sendiri.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Semua mahasiswa dari semua status, eager-load penilaian & user
            $mahasiswas = Mahasiswa::with(['penilaian.admin', 'user'])
                ->latest()
                ->paginate(10);

            return view('pages.admin.penilaian', compact('mahasiswas'));
        }

        // Mahasiswa: cek apakah sudah ditempatkan
        $mahasiswa = Mahasiswa::where('user_id', $user->id)
            ->with('penilaian.admin')
            ->first();

        if (!$mahasiswa || ($mahasiswa->status_pendaftaran !== 'ditempatkan' && $mahasiswa->status !== 'selesai')) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Anda belum dapat melihat penilaian. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.');
        }

        return view('pages.admin.penilaian', compact('mahasiswa'));
    }

    /**
     * Simpan atau update penilaian oleh Admin/Mentor.
     */
    public function store(Request $request): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya Admin/Mentor yang dapat memberikan penilaian.');
        }

        $existing = Penilaian::where('mahasiswa_id', $request->input('mahasiswa_id'))->first();

        $validated = $request->validate([
            'mahasiswa_id'          => ['required', 'exists:mahasiswas,id'],
            'nilai_akhir'           => ['required', 'numeric', 'min:0', 'max:100'],
            'catatan_mentor'        => ['required', 'string', 'min:10', 'max:1000'],
            'file_formulir_kampus'  => [
                $existing && $existing->file_formulir_kampus ? 'nullable' : 'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ], [
            'nilai_akhir.required'          => 'Nilai akhir wajib diisi.',
            'nilai_akhir.numeric'           => 'Nilai akhir harus berupa angka.',
            'nilai_akhir.min'               => 'Nilai akhir minimal 0.',
            'nilai_akhir.max'               => 'Nilai akhir maksimal 100.',
            'catatan_mentor.required'       => 'Catatan dari mentor wajib diisi.',
            'catatan_mentor.min'            => 'Catatan dari mentor minimal berisi 10 karakter evaluasi yang jelas.',
            'file_formulir_kampus.required' => 'Formulir penilaian resmi dari kampus wajib diunggah.',
            'file_formulir_kampus.mimes'    => 'Format formulir harus berupa file PDF, JPG, JPEG, atau PNG.',
            'file_formulir_kampus.max'      => 'Ukuran file formulir maksimal 5MB.',
        ]);

        $nilaiAkhir = round((float) $validated['nilai_akhir'], 2);
        $predikat   = Penilaian::hitungPredikat($nilaiAkhir);

        // Pastikan laporan akhir mahasiswa sudah disetujui sebelum dapat dinilai
        $mahasiswa = Mahasiswa::find($validated['mahasiswa_id']);
        if (!$mahasiswa || $mahasiswa->status_laporan_akhir !== 'disetujui') {
            return redirect()->route('penilaian.index')
                ->with('error', 'Penilaian tidak dapat disimpan. Laporan akhir mahasiswa ' . ($mahasiswa->nama_lengkap ?? '') . ' belum disetujui. Setujui laporan akhirnya terlebih dahulu.');
        }

        $payload = [
            'mahasiswa_id'   => $validated['mahasiswa_id'],
            'admin_id'       => Auth::id(),
            'nilai_akhir'    => $nilaiAkhir,
            'predikat'       => $predikat,
            'catatan_mentor' => $validated['catatan_mentor'],
        ];

        if ($request->hasFile('file_formulir_kampus')) {
            if ($existing && $existing->file_formulir_kampus && Storage::disk('public')->exists($existing->file_formulir_kampus)) {
                Storage::disk('public')->delete($existing->file_formulir_kampus);
            }
            $payload['file_formulir_kampus'] = $request->file('file_formulir_kampus')->store('formulir-penilaian', 'public');
        }

        Penilaian::updateOrCreate(
            ['mahasiswa_id' => $validated['mahasiswa_id']],
            $payload
        );

        // Otomatis update status mahasiswa jadi 'selesai' jika belum
        if ($mahasiswa->status !== 'selesai') {
            $mahasiswa->update(['status' => 'selesai']);
        }

        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil disimpan. Nilai akhir: ' . $nilaiAkhir . ' (Predikat ' . $predikat . ')');
    }

    /**
     * Lihat atau unduh formulir penilaian resmi kampus.
     */
    public function unduhFormulir(Penilaian $penilaian): Response
    {
        $user = Auth::user();

        // Autorisasi: Admin atau mahasiswa bersangkutan
        if ($user->role !== 'admin') {
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            if (!$mahasiswa || $mahasiswa->id !== $penilaian->mahasiswa_id) {
                abort(403, 'Anda tidak memiliki hak untuk melihat berkas penilaian ini.');
            }
        }

        if (!$penilaian->file_formulir_kampus || !Storage::disk('public')->exists($penilaian->file_formulir_kampus)) {
            abort(404, 'File formulir penilaian kampus tidak ditemukan atau belum diunggah.');
        }

        $path = Storage::disk('public')->path($penilaian->file_formulir_kampus);
        $mime = Storage::disk('public')->mimeType($penilaian->file_formulir_kampus) ?? 'application/octet-stream';
        $filename = 'Formulir-Penilaian-' . str()->slug($penilaian->mahasiswa?->nama_lengkap ?? 'mahasiswa') . '.' . pathinfo($path, PATHINFO_EXTENSION);

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
