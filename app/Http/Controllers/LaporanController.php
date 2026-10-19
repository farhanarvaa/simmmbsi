<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        if (!$isAdmin) {
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            if (!$mahasiswa || ($mahasiswa->status_pendaftaran !== 'ditempatkan' && $mahasiswa->status !== 'selesai')) {
                return redirect()->route('mahasiswa.dashboard')->with('error', 'Anda belum dapat mengakses laporan akhir. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.');
            }

            return view('pages.mahasiswa.laporan', compact('mahasiswa', 'isAdmin'));
        }

        // Admin: daftar laporan seluruh mahasiswa
        $mahasiswas = Mahasiswa::whereNotNull('laporan_akhir')
            ->orWhereIn('status_pendaftaran', ['diterima', 'ditempatkan'])
            ->latest('tanggal_laporan_akhir')
            ->latest()
            ->paginate(10);

        return view('pages.mahasiswa.laporan', compact('mahasiswas', 'isAdmin'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            abort(403, 'Admin/Mentor tidak perlu mengunggah laporan.');
        }

        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        if (!$mahasiswa || ($mahasiswa->status_pendaftaran !== 'ditempatkan' && $mahasiswa->status !== 'selesai')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Anda belum dapat mengakses laporan akhir. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.'], 403);
            }
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Anda belum dapat mengakses laporan akhir. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.');
        }

        // Blokir upload ulang jika laporan sudah disetujui
        if ($mahasiswa->status_laporan_akhir === 'disetujui') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Laporan akhir Anda sudah disetujui dan tidak dapat diubah lagi.'], 403);
            }
            return redirect()->route('laporan.index')->with('error', 'Laporan akhir Anda sudah disetujui dan tidak dapat diubah lagi.');
        }

        $validated = $request->validate([
            'laporan_akhir' => ['required', 'file', 'mimes:pdf', 'max:5120'], // Max 5MB (5120 KB)
        ], [
            'laporan_akhir.required' => 'Silakan pilih file laporan akhir PDF yang ingin diunggah.',
            'laporan_akhir.mimes' => 'File laporan harus berformat PDF (.pdf).',
            'laporan_akhir.max' => 'Ukuran file laporan maksimal 5MB.',
        ]);

        $file = $request->file('laporan_akhir');
        $originalName = $file->getClientOriginalName();
        $oldFilePath = $mahasiswa->laporan_akhir;

        $storedPath = $file->store('laporan_akhir', 'public');

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($mahasiswa, $storedPath, $originalName, $oldFilePath) {
                // Hapus file lama jika sebelumnya sudah ada
                if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }

                $mahasiswa->update([
                    'laporan_akhir' => $storedPath,
                    'nama_file_laporan' => $originalName,
                    'status_laporan_akhir' => 'menunggu_verifikasi',
                    'tanggal_laporan_akhir' => now(),
                    'catatan_laporan_akhir' => null,
                ]);
            });
        } catch (\Throwable $e) {
            if (Storage::disk('public')->exists($storedPath)) {
                Storage::disk('public')->delete($storedPath);
            }
            throw $e;
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan akhir berhasil diunggah dan sedang menunggu verifikasi.',
                'data' => [
                    'nama_file' => $originalName,
                    'file_url' => asset('storage/' . $storedPath),
                    'status' => 'menunggu_verifikasi',
                    'status_label' => 'Menunggu Verifikasi',
                    'tanggal' => Carbon::now()->isoFormat('D MMMM Y, HH:mm') . ' WIB',
                ]
            ]);
        }

        return redirect()->route('laporan.index')->with('success', 'Laporan akhir berhasil diunggah dan sedang menunggu verifikasi.');
    }

    public function verifikasi(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'status_laporan_akhir' => ['required', 'in:disetujui,revisi'],
            'catatan_laporan_akhir' => ['nullable', 'string'],
        ]);

        $mahasiswa->update($validated);

        $statusText = $validated['status_laporan_akhir'] === 'disetujui' ? 'disetujui' : 'diminta untuk direvisi';
        return redirect()->back()->with('success', "Status laporan akhir mahasiswa {$mahasiswa->nama_lengkap} berhasil {$statusText}.");
    }
}
