<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Siswa;
use App\Models\DataSma;
use App\Models\JalurPendaftaran;

class DummySiswaSeeder extends Seeder
{
    public function run(): void
    {
        $count = 77;
        $fixedSmaId = 8;
        $fixedJalurId = 1;

        // 🚨 (Opsional) Pastikan data relasi ID 1 ada
        if (!DataSma::find($fixedSmaId)) {
            $this->command->error("DataSma dengan ID {$fixedSmaId} tidak ditemukan. Silakan tambahkan data ini.");
            return;
        }
        if (!JalurPendaftaran::find($fixedJalurId)) {
            $this->command->error("JalurPendaftaran dengan ID {$fixedJalurId} tidak ditemukan. Silakan tambahkan data ini.");
            return;
        }

        $this->command->info("Membuat {$count} user dan siswa dengan sma_id {$fixedSmaId} dan jalur_id {$fixedJalurId}...");
        
        // Menjalankan factory User, yang secara otomatis membuat Siswa via closure
        User::factory()
            ->count($count)
            ->create();

        $this->command->info("Selesai. Total {$count} user dan siswa berhasil dibuat.");
    }
}