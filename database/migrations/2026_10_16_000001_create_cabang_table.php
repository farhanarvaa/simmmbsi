<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabang', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        DB::table('cabang')->insert(array_map(
            fn (string $nama) => ['nama' => $nama, 'created_at' => now(), 'updated_at' => now()],
            [
                'KCP Yogyakarta Kusumanegara',
                'KC Yogyakarta Sudirman',
                'KC Yogyakarta Yos Sudarso / KCP Yogyakarta Kolonel Sugiyono',
                'KCP Ambarukmo',
                'KCP Bantul Melikan Lor',
                'KCP Bantul Sudirman 1',
                'KCP Godean 1',
                'KCP Godean 2',
                'KCP Kaliurang MM Lati',
                'KCP Kulon Progo',
                'KCP Magelang Muntilan',
                'KCP Magelang Square / KCP Magelang Gatot Subroto',
                'KCP Magelang Sudirman',
                'KCP Sleman 1',
                'KCP Sleman 2',
                'KCP Sleman Affandi',
                'KCP Sleman Prambanan',
                'KCP Terban / KCP Purworejo',
                'KCP Wonosari',
                'KCP Yogyakarta A Dahlan',
                'KCP Yogyakarta FTS UII',
                'KCP Yogyakarta FE UII',
                'KCP Yogyakarta UGM',
                'KCP Yogyakarta HOS Cokroaminoto',
                'KCP Yogyakarta Kaliurang',
                'KCP Yogyakarta Katamso',
                'KCP Yogyakarta Kotagede',
                'KK Yogyakarta FTS UII',
                'KK Yogyakarta RS PKU Muhammadiyah',
                'KK Yogyakarta UMY / KCP Yogyakarta UMY',
            ]
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('cabang');
    }
};
