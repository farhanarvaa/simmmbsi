<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\Penilaian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PenilaianSimplificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $studentUser;
    private Mahasiswa $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::create([
            'name'     => 'Mentor Admin BSI',
            'email'    => 'admin_bsi@bankbsi.co.id',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $this->studentUser = User::create([
            'name'     => 'Ahmad Mahasiswa',
            'email'    => 'ahmad@kampus.ac.id',
            'password' => bcrypt('password123'),
            'role'     => 'mahasiswa',
        ]);

        $this->mahasiswa = Mahasiswa::create([
            'user_id'               => $this->studentUser->id,
            'nama_lengkap'          => 'Ahmad Mahasiswa',
            'nim'                   => '12345678',
            'universitas'           => 'Universitas BSI',
            'fakultas'              => 'Teknologi Informasi',
            'jurusan'               => 'Sistem Informasi',
            'semester'              => 7,
            'no_hp'                 => '081234567890',
            'status_pendaftaran'    => 'ditempatkan',
            'status'                => 'aktif',
            'status_laporan_akhir'  => 'disetujui',
        ]);
    }

    public function test_admin_can_submit_simplified_penilaian_with_file_upload(): void
    {
        $file = UploadedFile::fake()->create('formulir_evaluasi_kampus.pdf', 1500, 'application/pdf');

        $response = $this->actingAs($this->admin)->post(route('penilaian.store'), [
            'mahasiswa_id'         => $this->mahasiswa->id,
            'nilai_akhir'          => 88.5,
            'file_formulir_kampus' => $file,
            'catatan_mentor'       => 'Mahasiswa menunjukkan kinerja yang sangat baik selama magang.',
        ]);

        $response->assertRedirect(route('penilaian.index'));
        $response->assertSessionHas('success');

        $penilaian = Penilaian::where('mahasiswa_id', $this->mahasiswa->id)->first();
        $this->assertNotNull($penilaian);
        $this->assertEquals(88.5, (float) $penilaian->nilai_akhir);
        $this->assertEquals('A', $penilaian->predikat);
        $this->assertEquals('Mahasiswa menunjukkan kinerja yang sangat baik selama magang.', $penilaian->catatan_mentor);
        $this->assertNotNull($penilaian->file_formulir_kampus);
        Storage::disk('public')->assertExists($penilaian->file_formulir_kampus);

        // Status mahasiswa otomatis berubah menjadi selesai
        $this->assertEquals('selesai', $this->mahasiswa->fresh()->status);
    }

    public function test_validation_fails_if_catatan_mentor_is_less_than_10_characters(): void
    {
        $file = UploadedFile::fake()->create('formulir.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)->post(route('penilaian.store'), [
            'mahasiswa_id'         => $this->mahasiswa->id,
            'nilai_akhir'          => 90,
            'file_formulir_kampus' => $file,
            'catatan_mentor'       => 'Kurang', // Kurang dari 10 karakter
        ]);

        $response->assertSessionHasErrors('catatan_mentor');
        $this->assertDatabaseCount('penilaians', 0);
    }

    public function test_validation_fails_if_file_formulir_is_not_supported_format(): void
    {
        $file = UploadedFile::fake()->create('dokumen.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($this->admin)->post(route('penilaian.store'), [
            'mahasiswa_id'         => $this->mahasiswa->id,
            'nilai_akhir'          => 80,
            'file_formulir_kampus' => $file,
            'catatan_mentor'       => 'Mahasiswa cukup aktif dalam kegiatan operasional kantor cabang.',
        ]);

        $response->assertSessionHasErrors('file_formulir_kampus');
        $this->assertDatabaseCount('penilaians', 0);
    }

    public function test_student_can_view_penilaian_page_with_nilai_akhir_and_download_formulir(): void
    {
        $file = UploadedFile::fake()->create('formulir_resmi.pdf', 800, 'application/pdf');
        $filePath = $file->store('formulir-penilaian', 'public');

        $penilaian = Penilaian::create([
            'mahasiswa_id'         => $this->mahasiswa->id,
            'admin_id'             => $this->admin->id,
            'nilai_akhir'          => 82.0,
            'predikat'             => 'B',
            'file_formulir_kampus' => $filePath,
            'catatan_mentor'       => 'Kerja sama tim dan komunikasi sangat baik selama magang berlangsung.',
        ]);

        // Akses halaman penilaian sebagai mahasiswa
        $response = $this->actingAs($this->studentUser)->get(route('penilaian.index'));
        $response->assertOk();
        $response->assertSee('82.0');
        $response->assertSee('Predikat B');
        $response->assertSee('Lihat/Unduh Formulir Penilaian dari Kampus');
        $response->assertSee('Kerja sama tim dan komunikasi sangat baik selama magang berlangsung.');
        $response->assertDontSee('Detail Per Aspek Penilaian');

        // Unduh/lihat formulir resmi
        $downloadResponse = $this->actingAs($this->studentUser)->get(route('penilaian.formulir', $penilaian->id));
        $downloadResponse->assertOk();
    }

    public function test_other_student_cannot_download_different_student_formulir(): void
    {
        $file = UploadedFile::fake()->create('formulir_resmi.pdf', 800, 'application/pdf');
        $filePath = $file->store('formulir-penilaian', 'public');

        $penilaian = Penilaian::create([
            'mahasiswa_id'         => $this->mahasiswa->id,
            'admin_id'             => $this->admin->id,
            'nilai_akhir'          => 82.0,
            'predikat'             => 'B',
            'file_formulir_kampus' => $filePath,
            'catatan_mentor'       => 'Kerja sama tim dan komunikasi sangat baik selama magang berlangsung.',
        ]);

        $otherStudentUser = User::create([
            'name'     => 'Bambang Mahasiswa Lain',
            'email'    => 'bambang@kampus.ac.id',
            'password' => bcrypt('password123'),
            'role'     => 'mahasiswa',
        ]);
        Mahasiswa::create([
            'user_id'            => $otherStudentUser->id,
            'nama_lengkap'       => 'Bambang Mahasiswa Lain',
            'nim'                => '87654321',
            'status_pendaftaran' => 'ditempatkan',
        ]);

        $response = $this->actingAs($otherStudentUser)->get(route('penilaian.formulir', $penilaian->id));
        $response->assertForbidden();
    }
}
