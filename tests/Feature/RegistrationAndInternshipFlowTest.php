<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationAndInternshipFlowTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test alur registrasi akun baru:
     * - Hanya mengisi nama, email, no WhatsApp, password
     * - Record Mahasiswa diinisialisasi kosong (NIM, universitas, fakultas, jurusan, semester = NULL)
     * - Status pendaftaran awal adalah 'belum_daftar'
     */
    public function test_new_user_registration_initializes_isolated_empty_academic_profile(): void
    {
        $uniqueEmail = 'mahasiswabaru_' . time() . '@kampus.ac.id';
        $noHp = '089876543210';

        $response = $this->post('/register', [
            'name' => 'Budi Pratama Baru',
            'email' => $uniqueEmail,
            'no_hp' => $noHp,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree' => 'on',
        ]);

        $response->assertRedirect('/mahasiswa/dashboard');

        $user = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($user);
        $this->assertEquals('mahasiswa', $user->role);
        $this->assertEquals($noHp, $user->no_hp);

        // Pastikan Mahasiswa terbuat terisolasi untuk user ini
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        $this->assertNotNull($mahasiswa);
        $this->assertEquals($user->name, $mahasiswa->nama_lengkap);
        $this->assertEquals($noHp, $mahasiswa->no_hp);
        $this->assertNull($mahasiswa->nim);
        $this->assertNull($mahasiswa->universitas);
        $this->assertNull($mahasiswa->fakultas);
        $this->assertNull($mahasiswa->jurusan);
        $this->assertNull($mahasiswa->semester);
        $this->assertEquals('belum_daftar', $mahasiswa->status_pendaftaran);
        $this->assertFalse($mahasiswa->isProfilAkademikLengkap());
    }

    /**
     * Test halaman profil akun baru:
     * - Menampilkan nama, email, no WhatsApp user
     * - Field akademik (NIM, universitas, fakultas, prodi, semester) bertuliskan "Belum diisi"
     * - Tidak menampilkan data dummy/user lain
     */
    public function test_profile_page_displays_belum_diisi_and_no_dummy_data_for_new_account(): void
    {
        $uniqueEmail = 'cekprofil_' . time() . '@kampus.ac.id';
        $noHp = '081122334455';

        $user = User::create([
            'name' => 'Siti Aisyah Baru',
            'email' => $uniqueEmail,
            'no_hp' => $noHp,
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nama_lengkap' => $user->name,
            'no_hp' => $user->no_hp,
            'nim' => null,
            'universitas' => null,
            'fakultas' => null,
            'jurusan' => null,
            'semester' => null,
            'status_pendaftaran' => 'belum_daftar',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->get('/profil');

        $response->assertStatus(200);
        $response->assertSee('Siti Aisyah Baru');
        $response->assertSee($uniqueEmail);
        $response->assertSee($noHp);
        $response->assertSee('Belum diisi');
        $response->assertSee('BELUM MENGAJUKAN MAGANG');

        // Pastikan tidak ada data dummy hardcoded yang muncul
        $response->assertDontSee('Fakultas Ekonomi & Bisnis Islam');
        $response->assertDontSee('0812-3456-7890');
    }

    /**
     * Test klik "Ajukan Pendaftaran Magang" tanpa melengkapi profil:
     * - Sistem memeriksa kelengkapan profil akademik
     * - Jika belum lengkap, wajib dialihkan ke form "Lengkapi Profil"
     */
    public function test_clicking_ajukan_pendaftaran_redirects_to_lengkapi_profil_if_incomplete(): void
    {
        $user = User::create([
            'name' => 'Dimas Anggara',
            'email' => 'dimas_' . time() . '@kampus.ac.id',
            'no_hp' => '087788990011',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        Mahasiswa::create([
            'user_id' => $user->id,
            'nama_lengkap' => $user->name,
            'no_hp' => $user->no_hp,
            'nim' => null,
            'universitas' => null,
            'fakultas' => null,
            'jurusan' => null,
            'semester' => null,
            'status_pendaftaran' => 'belum_daftar',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->get('/mahasiswa/pendaftaran/ajukan');

        $response->assertRedirect('/mahasiswa/profil/lengkapi');
        $response->assertSessionHas('warning');

        // Cek halaman form lengkapi profil
        $formResponse = $this->actingAs($user)->get('/mahasiswa/profil/lengkapi');
        $formResponse->assertStatus(200);
        $formResponse->assertSee('Lengkapi Profil Akademik');
        $formResponse->assertSee('Formulir Kelengkapan Data Akademik');
    }

    /**
     * Test alur lengkap:
     * 1. Lengkapi profil akademik
     * 2. Lanjut ke upload berkas
     * 3. Kirim berkas -> status jadi "diajukan" dan masuk antrian admin
     */
    public function test_full_internship_submission_flow_after_completing_profile(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi_' . time() . '@kampus.ac.id',
            'no_hp' => '081234432100',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nama_lengkap' => $user->name,
            'no_hp' => $user->no_hp,
            'nim' => null,
            'universitas' => null,
            'fakultas' => null,
            'jurusan' => null,
            'semester' => null,
            'status_pendaftaran' => 'belum_daftar',
            'status' => 'aktif',
        ]);

        $nim = '211152' . rand(1000, 9999);

        // 1. Submit kelengkapan profil akademik
        $profilResponse = $this->actingAs($user)->post('/mahasiswa/profil/lengkapi', [
            'nim' => $nim,
            'universitas' => 'Universitas Gadjah Mada',
            'fakultas' => 'Fakultas MIPA',
            'jurusan' => 'Ilmu Komputer',
            'semester' => 'Semester 7',
            'no_hp' => '081234432100',
        ]);

        $profilResponse->assertRedirect('/mahasiswa/pendaftaran/berkas');
        $profilResponse->assertSessionHas('success');

        $mahasiswa->refresh();
        $this->assertTrue($mahasiswa->isProfilAkademikLengkap());
        $this->assertEquals($nim, $mahasiswa->nim);
        $this->assertEquals('Universitas Gadjah Mada', $mahasiswa->universitas);
        $this->assertEquals('Fakultas MIPA', $mahasiswa->fakultas);
        $this->assertEquals('Ilmu Komputer', $mahasiswa->jurusan);
        $this->assertEquals('Semester 7', $mahasiswa->semester);

        // 2. Akses halaman upload berkas
        $berkasPageResponse = $this->actingAs($user)->get('/mahasiswa/pendaftaran/berkas');
        $berkasPageResponse->assertStatus(200);
        $berkasPageResponse->assertSee('Unggah Berkas Persyaratan Magang');
        $berkasPageResponse->assertSee($nim);

        // 3. Upload berkas (proposal & surat pengantar)
        $proposalFile = UploadedFile::fake()->create('proposal_magang.pdf', 500, 'application/pdf');
        $suratFile = UploadedFile::fake()->create('surat_pengantar.pdf', 300, 'application/pdf');

        $uploadResponse = $this->actingAs($user)->post('/mahasiswa/pendaftaran', [
            'proposal_magang' => $proposalFile,
            'surat_pengantar' => $suratFile,
            'tanggal_mulai' => '2026-11-01',
            'tanggal_selesai' => '2027-01-31',
        ]);

        $uploadResponse->assertRedirect('/mahasiswa/dashboard');
        $uploadResponse->assertSessionHas('success');

        $mahasiswa->refresh();
        // 6. Status pendaftaran berubah jadi "diajukan"
        $this->assertEquals('diajukan', $mahasiswa->status_pendaftaran);
        $this->assertNotNull($mahasiswa->proposal_magang);
        $this->assertNotNull($mahasiswa->surat_pengantar);

        // Verifikasi masuk ke antrean admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $adminResponse = $this->actingAs($admin)->get('/mahasiswas');
            $adminResponse->assertStatus(200);
            $adminResponse->assertSee($nim);
            $adminResponse->assertSee('Dewi Lestari');
        }
    }
}
