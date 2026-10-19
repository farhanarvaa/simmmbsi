<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    protected $table = 'cabang';

    protected $fillable = ['nama'];

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'id_cabang');
    }
}
