<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat user baru terlebih dahulu (atau ambil user yang sudah ada)
        $user = User::create([
            'name' => 'Yovi Fajar Syahputra',
            'email' => 'yovi@example.com',
            'password' => Hash::make('password'),
            'role' => 'siswa', // Sesuaikan dengan sistem role aplikasi Anda
        ]);

        // 2. Buat data siswa yang berelasi dengan user_id di atas
        Siswa::create([
            'user_id' => $user->id,
            'data_sma_id' => 1, // Pastikan ID data SMA ini sudah ada di tabel data_smas
            'nisn' => '1234567890',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2008-05-15',
            'kabupaten' => 'Contoh Kabupaten',
            'kecamatan' => 'Contoh Kecamatan',
            'desa' => 'Contoh Desa',
            'alamat' => 'Jl. Contoh Alamat No. 123',
            'no_kk' => '3201234567890001',
            'nik' => '3201234567890002',
            'no_hp' => '081234567890',
            'nama_ayah' => 'Nama Ayah',
            'nama_ibu' => 'Nama Ibu',
            'agama' => 'Islam',
        ]);
    }
}