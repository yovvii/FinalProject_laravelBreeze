<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;

class UpdateAfirmasiSeeder extends Seeder
{
    public function run(): void
    {
        $randomDistance = mt_rand(100, 2000) / 100;
        $updateData = [
            'document_afirmasi' => 'dummy/document/afirmasi_seeder.pdf',
            'rapor_files_verified' => 'terverifikasi',
            'akta_file_verified' => 'terverifikasi',
            'surat_pernyataan_verified' => 'terverifikasi',
            'surat_keterangan_lulus_verified' => 'terverifikasi',
            'ijazah_file_verified' => 'terverifikasi',
            'verifikasi_afirmasi' => 'terverifikasi',
            'jarak_ke_sma_km' => $randomDistance,
        ];

        $count = Siswa::where('jalur_pendaftaran_id', 2)->update($updateData);

        $this->command->info("Total {$count} data Siswa (Jalur ID 2) berhasil diupdate menjadi terverifikasi.");
    }
}