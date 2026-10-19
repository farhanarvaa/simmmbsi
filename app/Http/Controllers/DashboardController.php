<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Mahasiswa;
use App\Models\Penilaian;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $totalMahasiswa = Mahasiswa::count();
        $mhsMingguIni = Mahasiswa::where('created_at', '>=', Carbon::now()->startOfWeek())->count();

        $mahasiswaAktifCount = Mahasiswa::where('status_pendaftaran', 'ditempatkan')
            ->where('status', 'aktif')
            ->count();

        $presensiHariIniCount = Presensi::whereDate('tanggal', Carbon::today())->count();
        $belumPresensiCount = max(0, $mahasiswaAktifCount - $presensiHariIniCount);

        $logbookDisetujuiCount = Logbook::where('status', 'disetujui')->count();
        $logbookDiajukanCount = Logbook::where('status', 'diajukan')->count();

        $sertifikatReadyCount = Penilaian::count();

        $totalDiterima = Mahasiswa::where('status_pendaftaran', 'ditempatkan')->count();
        $belumLaporRO = Mahasiswa::where('status_pendaftaran', 'ditempatkan')
            ->where('status_pelaporan_ro', 'belum_dilaporkan')
            ->count();

        $pendingLogbooks = Logbook::with('user')
            ->where('status', 'diajukan')
            ->latest('tanggal')
            ->take(5)
            ->get();

        $mahasiswaPenilaian = Mahasiswa::with(['penilaian.admin', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalMahasiswa',
            'mhsMingguIni',
            'mahasiswaAktifCount',
            'presensiHariIniCount',
            'belumPresensiCount',
            'logbookDisetujuiCount',
            'logbookDiajukanCount',
            'sertifikatReadyCount',
            'totalDiterima',
            'belumLaporRO',
            'pendingLogbooks',
            'mahasiswaPenilaian'
        ));
    }

    public function mahasiswaDashboard()
    {
        return view('dashboard.mahasiswa');
    }
}
