<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaians';

    protected $fillable = [
        'mahasiswa_id',
        'admin_id',
        'nilai_kedisiplinan',
        'nilai_kerjasama',
        'nilai_inisiatif',
        'nilai_kualitas_kerja',
        'nilai_komunikasi',
        'nilai_akhir',
        'predikat',
        'catatan_mentor',
    ];

    protected $casts = [
        'nilai_akhir' => 'decimal:2',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Hitung predikat otomatis dari nilai akhir.
     */
    public static function hitungPredikat(float $nilaiAkhir): string
    {
        if ($nilaiAkhir >= 85) return 'A';
        if ($nilaiAkhir >= 70) return 'B';
        if ($nilaiAkhir >= 55) return 'C';
        return 'D';
    }
}
