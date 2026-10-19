<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->string('laporan_akhir')->nullable()->after('transkrip');
            $table->string('nama_file_laporan')->nullable()->after('laporan_akhir');
            $table->string('status_laporan_akhir')->default('belum_unggah')->after('nama_file_laporan');
            $table->timestamp('tanggal_laporan_akhir')->nullable()->after('status_laporan_akhir');
            $table->text('catatan_laporan_akhir')->nullable()->after('tanggal_laporan_akhir');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn([
                'laporan_akhir',
                'nama_file_laporan',
                'status_laporan_akhir',
                'tanggal_laporan_akhir',
                'catatan_laporan_akhir'
            ]);
        });
    }
};
