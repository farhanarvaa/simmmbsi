<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('nilai_kedisiplinan')->default(0);
            $table->unsignedTinyInteger('nilai_kerjasama')->default(0);
            $table->unsignedTinyInteger('nilai_inisiatif')->default(0);
            $table->unsignedTinyInteger('nilai_kualitas_kerja')->default(0);
            $table->unsignedTinyInteger('nilai_komunikasi')->default(0);
            $table->decimal('nilai_akhir', 5, 2)->default(0);
            $table->enum('predikat', ['A', 'B', 'C', 'D'])->default('D');
            $table->text('catatan_mentor')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};
