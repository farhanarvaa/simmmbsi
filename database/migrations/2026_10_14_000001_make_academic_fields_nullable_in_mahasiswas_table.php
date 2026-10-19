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
            $table->string('nim')->nullable()->change();
            $table->string('universitas')->nullable()->change();
            $table->string('jurusan')->nullable()->change();
            $table->string('status_pendaftaran')->default('belum_daftar')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->string('nim')->nullable(false)->change();
            $table->string('universitas')->nullable(false)->change();
            $table->string('jurusan')->nullable(false)->change();
            $table->string('status_pendaftaran')->default('diajukan')->change();
        });
    }
};
