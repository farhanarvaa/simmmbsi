<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\SuratKeterangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SertifikasiController extends Controller
{
    /**
     * Menampilkan halaman kelola sertifikat magang.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        if ($isAdmin) {
            $mahasiswas = Mahasiswa::with(['penilaian.admin', 'user', 'suratKeterangan'])
                ->where(function ($q) {
                    $q->where('status', 'selesai')->orWhereHas('penilaian');
                })
                ->latest()
                ->paginate(10);

            return view('pages.admin.sertifikasi', compact('mahasiswas', 'isAdmin'));
        }

        $mahasiswa = Mahasiswa::with(['penilaian.admin', 'user', 'suratKeterangan'])
            ->where('user_id', $user->id)
            ->first();

        if (!$mahasiswa || ($mahasiswa->status_pendaftaran !== 'ditempatkan' && $mahasiswa->status !== 'selesai')) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Anda belum dapat mengakses sertifikasi. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.');
        }

        return view('pages.admin.sertifikasi', [
            'mahasiswa' => $mahasiswa,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function issue(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $mahasiswa->load('penilaian');
        if (!$this->isReadyForIssuance($mahasiswa)) {
            return back()->with('error', 'Surat belum dapat diterbitkan. Pastikan seluruh nilai lapangan dan data periode magang sudah lengkap.');
        }

        $surat = DB::transaction(function () use ($mahasiswa) {
            $existing = SuratKeterangan::where('mahasiswa_id', $mahasiswa->id)->first();
            if (!$existing) {
                $now = now();
                $year = (int) $now->format('Y');
                DB::table('surat_counters')->insertOrIgnore([
                    'tahun' => $year,
                    'nomor_terakhir' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $counter = DB::table('surat_counters')->where('tahun', $year)->lockForUpdate()->first();
                $number = $counter->nomor_terakhir + 1;
                DB::table('surat_counters')->where('tahun', $year)->update([
                    'nomor_terakhir' => $number,
                    'updated_at' => $now,
                ]);

                $monthCode = $this->romanMonth((int) $now->format('n'));
                $surat = SuratKeterangan::create([
                    'mahasiswa_id' => $mahasiswa->id,
                    'issued_by' => Auth::id(),
                    'nomor_urut' => $number,
                    'kode_bulan' => $monthCode,
                    'tahun' => $year,
                    'nomor_surat' => str_pad((string) $number, 2, '0', STR_PAD_LEFT) . '/' . $monthCode . '/' . $year . '/BSIYK',
                    'file_path' => 'surat-keterangan/pending-' . $mahasiswa->id . '.pdf',
                    'issued_at' => $now,
                ]);
            } else {
                $surat = $existing;
            }

            $start = Carbon::parse($mahasiswa->tanggal_mulai);
            $end = Carbon::parse($mahasiswa->tanggal_selesai);
            $interval = $start->diff($end);
            $duration = trim(($interval->y ? $interval->y . ' tahun ' : '') . ($interval->m ? $interval->m . ' bulan ' : '') . ($interval->d ? $interval->d . ' hari' : '')) ?: '0 hari';
            $logoPath = public_path('images/logo_bsi.png');
            $logoData = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
            $pdf = Pdf::loadView('pdf.suratketerangan', compact('mahasiswa', 'surat', 'duration', 'logoData') + [
                'startDate' => $start->translatedFormat('d F Y'),
                'endDate' => $end->translatedFormat('d F Y'),
            ])->setPaper('a4', 'portrait');
            $filePath = 'surat-keterangan/' . $surat->nomor_urut . '-' . $mahasiswa->id . '.pdf';
            Storage::disk('local')->put($filePath, $pdf->output());
            $surat->update(['file_path' => $filePath]);

            return $surat;
        });

        return back()->with('success', 'Surat keterangan ' . ($surat->nomor_surat) . ' berhasil diterbitkan.');
    }

    public function download(Mahasiswa $mahasiswa): BinaryFileResponse
    {
        $user = Auth::user();
        abort_unless($user->role === 'admin' || $mahasiswa->user_id === $user->id, 403);

        $surat = $mahasiswa->suratKeterangan;
        abort_unless($surat && Storage::disk('local')->exists($surat->file_path), 404);

        return response()->download(
            Storage::disk('local')->path($surat->file_path),
            'Surat-Keterangan-Selesai-Magang-' . str()->slug($mahasiswa->nama_lengkap) . '.pdf'
        );
    }

    public function preview(Mahasiswa $mahasiswa): BinaryFileResponse
    {
        $user = Auth::user();
        abort_unless($user->role === 'admin' || $mahasiswa->user_id === $user->id, 403);

        $surat = $mahasiswa->suratKeterangan;
        abort_unless($surat && Storage::disk('local')->exists($surat->file_path), 404);

        return response()->file(Storage::disk('local')->path($surat->file_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Surat-Keterangan-Selesai-Magang-' . str()->slug($mahasiswa->nama_lengkap) . '.pdf"',
        ]);
    }

    private function isReadyForIssuance(Mahasiswa $mahasiswa): bool
    {
        $penilaian = $mahasiswa->penilaian;
        return $penilaian
            && $penilaian->nilai_akhir !== null
            && filled($mahasiswa->nama_lengkap)
            && filled($mahasiswa->nim)
            && filled($mahasiswa->jurusan)
            && filled($mahasiswa->fakultas)
            && filled($mahasiswa->universitas)
            && filled($mahasiswa->tanggal_mulai)
            && filled($mahasiswa->tanggal_selesai);
    }

    private function romanMonth(int $month): string
    {
        return [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'][$month];
    }
}
