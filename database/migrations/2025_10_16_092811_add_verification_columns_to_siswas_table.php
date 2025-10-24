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
            // Kolom Verifikasi Dokumen Umum
            // Default: 'pending', Pilihan: 'terverifikasi', 'ditolak'
            $table->enum('rapor_files_verified', ['pending', 'terverifikasi', 'ditolak'])
                  ->default('pending')->after('nilai_akhir'); // Tempatkan setelah nilai_akhir
            
            $table->enum('surat_pernyataan_verified', ['pending', 'terverifikasi', 'ditolak'])
                  ->default('pending')->after('surat_pernyataan');
            
            $table->enum('surat_keterangan_lulus_verified', ['pending', 'terverifikasi', 'ditolak'])
                  ->default('pending')->after('surat_keterangan_lulus');
            
            $table->enum('ijazah_file_verified', ['pending', 'terverifikasi', 'ditolak'])
                  ->default('pending')->after('ijazah_file');
                  
            // Jika Anda juga perlu kolom verifikasi khusus jalur (prestasi/afirmasi)
            // Pastikan kolom ini sudah ada di tabel siswas Anda. Jika belum, tambahkan:
            // $table->enum('verifikasi_sertifikat', ['pending', 'terverifikasi', 'ditolak'])->nullable()->after('sertifikat_file');
            // $table->enum('verifikasi_afirmasi', ['pending', 'terverifikasi', 'ditolak'])->nullable()->after('document_afirmasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'rapor_files_verified',
                'surat_pernyataan_verified',
                'surat_keterangan_lulus_verified',
                'ijazah_file_verified',
                // 'verifikasi_sertifikat', // Hapus jika sebelumnya belum ada
                // 'verifikasi_afirmasi', // Hapus jika sebelumnya belum ada
            ]);
        });
    }
};