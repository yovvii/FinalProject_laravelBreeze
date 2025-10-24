<?php

namespace Database\Seeders;

use App\Models\SekolahAsal;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SekolahAsalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar nama sekolah yang akan di-seed
        $sekolahs = [
            // SMP Negeri
            'SMP Negeri 1 Purbalingga',
            'SMP Negeri 2 Purbalingga',
            'SMP Negeri 3 Purbalingga',
            'SMP Negeri 4 Purbalingga',
            'SMP Negeri 5 Purbalingga',
            
            // SMP Swasta
            'SMP Muhammadiyah 1 Purbalingga',
            'SMP Muhammadiyah 2 Bobotsari',
            'SMP Muhammadiyah 4 Kertanegara',
            'SMP Muhammadiyah 5 Losari Rembang',
            'SMP Muhammadiyah 6 Kaligondang',
            'SMP Muhammadiyah 8 Kemangkon',
            'SMP Muhammadiyah 10 Bojongsari',
            
            // MTs Negeri
            'MTs Negeri 1 Purbalingga',
            'MTs Negeri 2 Purbalingga',
            'MTs Negeri 3 Purbalingga',
            
            // MTs Swasta
            'MTs Muhammadiyah 1 Purbalingga',
            'MTs Muhammadiyah 2 Purbalingga',
            'MTs Muhammadiyah 3 Purbalingga',
            'MTs Muhammadiyah 4 Purbalingga',
            'MTs Muhammadiyah 5 Purbalingga',
            'MTs Muhammadiyah 6 Purbalingga',
            'MTs Muhammadiyah 7 Purbalingga',
            'MTs Muhammadiyah 8 Purbalingga',
            'MTs Muhammadiyah 9 Purbalingga',
            'MTs Muhammadiyah 10 Purbalingga',
            'MTs Muhammadiyah 11 Purbalingga',
            'MTs Usriyah Purbalingga',
            'MTs Minhajut Tholabah',
            'MTs Ma\'arif NU 06 Bojongsari',
            'MTs Ma\'arif NU 08 Panican',
        ];

        // Memasukkan data menggunakan firstOrCreate
        // Ini memastikan data tidak diduplikasi jika seeder dijalankan berulang
        foreach ($sekolahs as $sekolah) {
            SekolahAsal::firstOrCreate(['nama_sekolah' => $sekolah]);
        }
    }
}