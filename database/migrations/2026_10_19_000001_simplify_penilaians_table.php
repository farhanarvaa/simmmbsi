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
        Schema::table('penilaians', function (Blueprint $table) {
            $table->string('file_formulir_kampus')->nullable()->after('predikat');
            $table->dropColumn([
                'nilai_kedisiplinan',
                'nilai_kerjasama',
                'nilai_inisiatif',
                'nilai_kualitas_kerja',
                'nilai_komunikasi',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            $table->dropColumn('file_formulir_kampus');
            $table->unsignedTinyInteger('nilai_kedisiplinan')->default(0);
            $table->unsignedTinyInteger('nilai_kerjasama')->default(0);
            $table->unsignedTinyInteger('nilai_inisiatif')->default(0);
            $table->unsignedTinyInteger('nilai_kualitas_kerja')->default(0);
            $table->unsignedTinyInteger('nilai_komunikasi')->default(0);
        });
    }
};
