<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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

            return view('pages.penilaian', compact('mahasiswas'));
        }

        // Mahasiswa: cek apakah sudah ditempatkan
        $mahasiswa = Mahasiswa::where('user_id', $user->id)
            ->with('penilaian.admin')
            ->first();

        if (!$mahasiswa || ($mahasiswa->status_pendaftaran !== 'ditempatkan' && $mahasiswa->status !== 'selesai')) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Anda belum dapat melihat penilaian. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.');
        }

        return view('pages.penilaian', compact('mahasiswa'));
    }

    /**
     * Simpan atau update penilaian oleh Admin/Mentor.
     */
    public function store(Request $request): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya Admin/Mentor yang dapat memberikan penilaian.');
        }

        $validated = $request->validate([
            'mahasiswa_id'         => ['required', 'exists:mahasiswas,id'],
            'nilai_kedisiplinan'   => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_kerjasama'      => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_inisiatif'      => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_kualitas_kerja' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_komunikasi'     => ['required', 'integer', 'min:0', 'max:100'],
            'catatan_mentor'       => ['nullable', 'string', 'max:1000'],
        ]);

        // Hitung nilai akhir (rata-rata 5 aspek)
        $nilaiAkhir = (
            $validated['nilai_kedisiplinan'] +
            $validated['nilai_kerjasama'] +
            $validated['nilai_inisiatif'] +
            $validated['nilai_kualitas_kerja'] +
            $validated['nilai_komunikasi']
        ) / 5;

        $validated['nilai_akhir'] = round($nilaiAkhir, 2);
        $validated['predikat']    = Penilaian::hitungPredikat($nilaiAkhir);
        $validated['admin_id']    = Auth::id();

        // Pastikan laporan akhir mahasiswa sudah disetujui sebelum dapat dinilai
        $mahasiswa = Mahasiswa::find($validated['mahasiswa_id']);
        if (!$mahasiswa || $mahasiswa->status_laporan_akhir !== 'disetujui') {
            return redirect()->route('penilaian.index')
                ->with('error', 'Penilaian tidak dapat disimpan. Laporan akhir mahasiswa ' . ($mahasiswa->nama_lengkap ?? '') . ' belum disetujui. Setujui laporan akhirnya terlebih dahulu.');
        }

        Penilaian::updateOrCreate(
            ['mahasiswa_id' => $validated['mahasiswa_id']],
            $validated
        );

        // Otomatis update status mahasiswa jadi 'selesai' jika belum
        if ($mahasiswa->status !== 'selesai') {
            $mahasiswa->update(['status' => 'selesai']);
        }

        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil disimpan. Nilai akhir: ' . $validated['nilai_akhir'] . ' (Predikat ' . $validated['predikat'] . ')');
    }
}
