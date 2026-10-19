<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->string('proposal_magang')->nullable()->after('jurusan');
            $table->string('surat_pengantar')->nullable()->after('proposal_magang');
            $table->string('cv')->nullable()->after('surat_pengantar');
            $table->string('transkrip')->nullable()->after('cv');
            $table->string('divisi_bsi')->nullable()->change();
            $table->date('tanggal_mulai')->nullable()->change();
            $table->string('status_pendaftaran')->default('diajukan')->after('tanggal_selesai');
            $table->text('catatan_revisi')->nullable()->after('status_pendaftaran');
            $table->string('status_pelaporan_ro')->default('belum_dilaporkan')->after('catatan_revisi');
            $table->date('tanggal_pelaporan_ro')->nullable()->after('status_pelaporan_ro');
            $table->string('nomor_referensi_laporan_ro')->nullable()->after('tanggal_pelaporan_ro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'proposal_magang',
                'surat_pengantar',
                'cv',
                'transkrip',
                'status_pendaftaran',
                'catatan_revisi',
                'status_pelaporan_ro',
                'tanggal_pelaporan_ro',
                'nomor_referensi_laporan_ro'
            ]);
        });
    }
};
