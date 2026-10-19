<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_views_exist_in_laravel_view_finder(): void
    {
        $views = [
            'components.sidebar-admin',
            'components.sidebar-mahasiswa',
            'components.sidebaradmin',
            'components.sidebarmahasiswa',
            'components.topbar',
            'pages.admin.penempatan',
            'pages.admin.pengguna',
            'pages.admin.penilaian',
            'pages.admin.sertifikasi',
            'pages.mahasiswa.profil',
            'pages.mahasiswa.lengkapiprofil',
            'pages.mahasiswa.laporan',
            'pages.mahasiswa.uploadberkas',
            'pdf.suratketerangan',
        ];

        foreach ($views as $view) {
            $this->assertTrue(view()->exists($view), "View not found: {$view}");
        }
    }

    public function test_admin_can_access_all_admin_pages(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@bsi.co.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        Cabang::create(['nama' => 'KC Sudirman']);

        // Dashboard
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        // Pengguna (admin/pengguna)
        $response = $this->actingAs($admin)->get('/pengguna');
        $response->assertStatus(200);

        // Penempatan (admin/penempatan)
        $response = $this->actingAs($admin)->get('/penempatan');
        $response->assertStatus(200);

        // Penilaian (admin/penilaian)
        $response = $this->actingAs($admin)->get('/penilaian');
        $response->assertStatus(200);

        // Sertifikasi (admin/sertifikasi)
        $response = $this->actingAs($admin)->get('/sertifikasi');
        $response->assertStatus(200);

        // Laporan (mahasiswa/laporan rendered for admin)
        $response = $this->actingAs($admin)->get('/laporan');
        $response->assertStatus(200);

        // Mahasiswas index (mahasiswas/index)
        $response = $this->actingAs($admin)->get('/mahasiswas');
        $response->assertStatus(200);

        // Mahasiswas create
        $response = $this->actingAs($admin)->get('/mahasiswas/create');
        $response->assertStatus(200);
    }

    public function test_mahasiswa_can_access_all_mahasiswa_pages(): void
    {
        $user = User::create([
            'name' => 'Mhs Test',
            'email' => 'mhs@kampus.ac.id',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nama_lengkap' => $user->name,
            'status_pendaftaran' => 'belum_daftar',
            'status' => 'aktif',
        ]);

        // Dashboard
        $response = $this->actingAs($user)->get('/mahasiswa/dashboard');
        $response->assertStatus(200);

        // Profil (mahasiswa/profil)
        $response = $this->actingAs($user)->get('/profil');
        $response->assertStatus(200);

        // Lengkapi profil (mahasiswa/lengkapiprofil)
        $response = $this->actingAs($user)->get('/mahasiswa/profil/lengkapi');
        $response->assertStatus(200);

        // Set profil lengkap to access upload berkas
        $mahasiswa->update([
            'nim' => '12345678',
            'universitas' => 'UGM',
            'fakultas' => 'Teknik',
            'jurusan' => 'Informatika',
            'semester' => 6,
        ]);

        // Upload berkas (mahasiswa/uploadberkas)
        $response = $this->actingAs($user)->get('/mahasiswa/pendaftaran/berkas');
        $response->assertStatus(200);

        // Ditempatkan status to access laporan, penilaian, sertifikasi, presensi, logbooks
        $mahasiswa->update([
            'status_pendaftaran' => 'ditempatkan',
        ]);

        // Laporan
        $response = $this->actingAs($user)->get('/laporan');
        $response->assertStatus(200);

        // Penilaian
        $response = $this->actingAs($user)->get('/penilaian');
        $response->assertStatus(200);

        // Sertifikasi
        $response = $this->actingAs($user)->get('/sertifikasi');
        $response->assertStatus(200);

        // Penempatan (Surat Penerimaan Magang)
        $response = $this->actingAs($user)->get('/penempatan');
        $response->assertStatus(200);

        // Presensi
        $response = $this->actingAs($user)->get('/presensis');
        $response->assertStatus(200);

        // Presensi create
        $response = $this->actingAs($user)->get('/presensis/create');
        $response->assertStatus(200);

        // Logbooks
        $response = $this->actingAs($user)->get('/logbooks');
        $response->assertStatus(200);

        // Logbooks create
        $response = $this->actingAs($user)->get('/logbooks/create');
        $response->assertStatus(200);
    }

    public function test_pdf_suratketerangan_renders_successfully(): void
    {
        $user = User::create([
            'name' => 'PDF Test Mhs',
            'email' => 'pdfmhs@kampus.ac.id',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nama_lengkap' => $user->name,
            'nim' => '99887766',
            'universitas' => 'UGM',
            'fakultas' => 'Ekonomi',
            'jurusan' => 'Akuntansi',
            'semester' => 7,
            'status_pendaftaran' => 'ditempatkan',
            'status' => 'selesai',
            'tanggal_mulai' => now()->subMonths(3)->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
        ]);

        $html = view('pdf.suratketerangan', [
            'mahasiswa' => $mahasiswa,
            'surat' => (object)[
                'nomor_surat' => 'BSI/YOG/2026/001',
                'tanggal_terbit' => now(),
                'nama_penandatangan' => 'Budi Santoso',
                'jabatan_penandatangan' => 'Area Manager',
            ],
            'duration' => '3 bulan',
            'logoData' => '',
            'startDate' => now()->subMonths(3)->translatedFormat('d F Y'),
            'endDate' => now()->translatedFormat('d F Y'),
        ])->render();

        $this->assertNotEmpty($html);
        $this->assertStringContainsString('PDF Test Mhs', $html);
        $this->assertStringContainsString('BSI/YOG/2026/001', $html);
    }
}
