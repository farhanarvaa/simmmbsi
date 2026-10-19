<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_keterangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->unique()->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('issued_by')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('nomor_urut');
            $table->string('kode_bulan', 2);
            $table->unsignedSmallInteger('tahun');
            $table->string('nomor_surat')->unique();
            $table->string('file_path');
            $table->timestamp('issued_at');
            $table->timestamps();

            $table->unique(['nomor_urut', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_keterangans');
    }
};
