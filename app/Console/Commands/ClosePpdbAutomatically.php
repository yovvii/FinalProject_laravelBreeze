<?php

namespace App\Console\Commands;

use App\Models\SpmbStatus;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

class ClosePpdbAutomatically extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ppdb:close-auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if the PPDB should be closed automatically and runs selection.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $status = SpmbStatus::first();

        // 1. Cek: Apakah PPDB masih 'open' dan apakah waktu penutupan sudah lewat?
        if ($status && $status->status === 'open' && $status->closing_at && Carbon::now()->greaterThanOrEqualTo($status->closing_at)) {
            
            $this->info('Waktu penutupan PPDB otomatis telah tercapai. Menjalankan proses seleksi...');
            
            // 2. 🔥 Panggil Logika Penentuan Penerimaan Anda di sini
            // (Misalnya, panggil method dari Controller atau Service yang menangani logika PPDB stop)
            // Contoh: (Anda harus menyesuaikan ini dengan implementasi Anda)
            // app(\App\Services\PpdbService::class)->runSelectionAndClose(); 
            
            $status->status = 'closed';
            $status->save();
            
            $this->info('Proses seleksi PPDB otomatis selesai.');
            return 0;
        }

        $this->info('Tidak ada PPDB otomatis yang perlu ditutup saat ini.');
        return 0;
    }
}
