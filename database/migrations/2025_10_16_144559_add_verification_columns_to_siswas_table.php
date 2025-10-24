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
        Schema::table('siswas', function (Blueprint $table) {
            // Kolom Verifikasi Dokumen Wajib
            $table->enum('akta_file_verified', ['pending', 'terverifikasi', 'ditolak'])->default('pending')->after('akta_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'akta_file_verified',
            ]);
        });
    }
};