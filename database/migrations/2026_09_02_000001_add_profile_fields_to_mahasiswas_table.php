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
            if (!Schema::hasColumn('mahasiswas', 'fakultas')) {
                $table->string('fakultas')->nullable()->after('universitas');
            }
            if (!Schema::hasColumn('mahasiswas', 'no_hp')) {
                $table->string('no_hp')->nullable()->after('jurusan');
            }
            if (!Schema::hasColumn('mahasiswas', 'semester')) {
                $table->string('semester')->nullable()->after('no_hp');
            }
            if (!Schema::hasColumn('mahasiswas', 'nama_mentor')) {
                $table->string('nama_mentor')->nullable()->after('divisi_bsi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn(['fakultas', 'no_hp', 'semester', 'nama_mentor']);
        });
    }
};
