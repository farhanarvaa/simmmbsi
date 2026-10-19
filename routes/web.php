<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PenempatanController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\SertifikasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])
        ->middleware('role:admin')
        ->name('admin.dashboard');

    Route::get('/mahasiswa/dashboard', [DashboardController::class, 'mahasiswaDashboard'])
        ->middleware('role:mahasiswa')
        ->name('mahasiswa.dashboard');

    // Alur Pengajuan Magang Mahasiswa (Pemeriksaan Profil -> Upload Berkas -> Diajukan)
    Route::get('/mahasiswa/pendaftaran/ajukan', [MahasiswaController::class, 'ajukanPendaftaran'])
        ->middleware('role:mahasiswa')
        ->name('mahasiswa.pendaftaran.ajukan');

    Route::get('/mahasiswa/profil/lengkapi', [MahasiswaController::class, 'formLengkapiProfil'])
        ->middleware('role:mahasiswa')
        ->name('mahasiswa.profil.lengkapi');

    Route::post('/mahasiswa/profil/lengkapi', [MahasiswaController::class, 'updateLengkapiProfil'])
        ->middleware('role:mahasiswa')
        ->name('mahasiswa.profil.lengkapi.store');

    Route::get('/mahasiswa/pendaftaran/berkas', [MahasiswaController::class, 'formUploadBerkas'])
        ->middleware('role:mahasiswa')
        ->name('mahasiswa.pendaftaran.berkas');

    Route::post('/mahasiswa/pendaftaran', [MahasiswaController::class, 'storePendaftaran'])
        ->middleware('role:mahasiswa')
        ->name('mahasiswa.pendaftaran.store');

    Route::put('/mahasiswas/{mahasiswa}/verifikasi', [MahasiswaController::class, 'updateStatusPendaftaran'])
        ->middleware('role:admin')
        ->name('mahasiswas.verifikasi');

    Route::put('/mahasiswas/{mahasiswa}/lapor-ro', [MahasiswaController::class, 'laporRO'])
        ->middleware('role:admin')
        ->name('mahasiswas.lapor-ro');

    // Pengelolaan data Mahasiswa khusus Admin/Mentor
    Route::resource('mahasiswas', MahasiswaController::class)
        ->middleware('role:admin');

    Route::resource('presensis', PresensiController::class)
        ->middleware('role:admin,mahasiswa');

    Route::resource('logbooks', LogbookController::class)
        ->middleware('role:admin,mahasiswa');

    // Menu Penilaian
    Route::get('/penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
    Route::post('/penilaian', [PenilaianController::class, 'store'])->name('penilaian.store')->middleware('role:admin');

    // Menu Laporan Akhir
    Route::get('/laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan', [\App\Http\Controllers\LaporanController::class, 'store'])->name('laporan.store')->middleware('role:mahasiswa');
    Route::put('/laporan/{mahasiswa}/verifikasi', [\App\Http\Controllers\LaporanController::class, 'verifikasi'])->name('laporan.verifikasi')->middleware('role:admin');

    // Menu Penempatan
    Route::get('/penempatan', [PenempatanController::class, 'index'])->name('penempatan.index');
    Route::put('/penempatan/{mahasiswa}', [PenempatanController::class, 'update'])->name('penempatan.update')->middleware('role:admin');

    // Menu Sertifikasi
    Route::get('/sertifikasi', [SertifikasiController::class, 'index'])->name('sertifikasi.index');
    Route::post('/sertifikasi/{mahasiswa}/terbitkan', [SertifikasiController::class, 'issue'])
        ->middleware('role:admin')
        ->name('sertifikasi.issue');
    Route::get('/sertifikasi/{mahasiswa}/download', [SertifikasiController::class, 'download'])
        ->name('sertifikasi.download');
    Route::get('/sertifikasi/{mahasiswa}/preview', [SertifikasiController::class, 'preview'])
        ->name('sertifikasi.preview');

    // Menu Kelola Akun Admin/Mentor
    Route::get('/pengguna', [\App\Http\Controllers\AdminUserController::class, 'index'])->middleware('role:admin')->name('pengguna.index');
    Route::post('/pengguna', [\App\Http\Controllers\AdminUserController::class, 'store'])->middleware('role:admin')->name('pengguna.store');
    Route::put('/pengguna/{user}/reset-password', [\App\Http\Controllers\AdminUserController::class, 'resetPassword'])->middleware('role:admin')->name('pengguna.reset-password');
    Route::put('/pengguna/{user}/toggle-status', [\App\Http\Controllers\AdminUserController::class, 'toggleStatus'])->middleware('role:admin')->name('pengguna.toggle-status');

    // Menu Profil Pengguna
    Route::get('/profil', [\App\Http\Controllers\ProfilController::class, 'show'])->name('profil.show');
    Route::put('/profil', [\App\Http\Controllers\ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [\App\Http\Controllers\ProfilController::class, 'updatePassword'])->name('profil.password.update');
});