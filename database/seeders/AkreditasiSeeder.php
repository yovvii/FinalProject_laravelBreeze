<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AkreditasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('akreditasis')->insert([
            [
                'jenis_akreditasi' => 'A',
                'warna_background' => '#10B981', // Hijau (Emerald)
                'warna_text'       => '#FFFFFF', // Putih
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'jenis_akreditasi' => 'B',
                'warna_background' => '#3B82F6', // Biru
                'warna_text'       => '#FFFFFF', // Putih
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'jenis_akreditasi' => 'C',
                'warna_background' => '#F59E0B', // Amber / Kuning Tua
                'warna_text'       => '#FFFFFF', // Putih
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }
}