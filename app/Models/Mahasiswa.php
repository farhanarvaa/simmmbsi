<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswas';

    protected $fillable = [
        'user_id',
        'nim',
        'nama_lengkap',
        'universitas',
        'fakultas',
        'jurusan',
        'semester',
        'no_hp',
        'proposal_magang',
        'surat_pengantar',
        'cv',
        'transkrip',
        'laporan_akhir',
        'nama_file_laporan',
        'status_laporan_akhir',
        'tanggal_laporan_akhir',
        'catatan_laporan_akhir',
        'id_cabang',
        'nama_mentor',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_pendaftaran',
        'catatan_revisi',
        'status_pelaporan_ro',
        'tanggal_pelaporan_ro',
        'nomor_referensi_laporan_ro',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'id_cabang');
    }

    public function penilaian(): HasOne
    {
        return $this->hasOne(Penilaian::class);
    }

    public function suratKeterangan(): HasOne
    {
        return $this->hasOne(SuratKeterangan::class);
    }

    /**
     * Memeriksa apakah profil akademik mahasiswa telah lengkap:
     * NIM, universitas, fakultas, prodi (jurusan), dan semester.
     */
    public function isProfilAkademikLengkap(): bool
    {
        return !empty(trim($this->nim ?? '')) &&
               !empty(trim($this->universitas ?? '')) &&
               !empty(trim($this->fakultas ?? '')) &&
               !empty(trim($this->jurusan ?? '')) &&
               !empty(trim($this->semester ?? ''));
    }
}
