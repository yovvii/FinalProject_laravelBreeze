<?php

namespace App\Console\Commands;

use App\Models\SpmbStatus;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

class OpenPpdbAutomatically extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ppdb:open-auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if the PPDB should be opened automatically.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $status = SpmbStatus::firstOrNew();

        // 1. Cek: Apakah status bukan 'open' dan apakah waktu mulai sudah tiba?
        if ($status->status !== 'open' && $status->starting_at && Carbon::now()->greaterThanOrEqualTo($status->starting_at)) {
            
            $this->info('Waktu pembukaan PPDB otomatis telah tercapai. Membuka pendaftaran...');
            
            $status->status = 'open';
            $status->save();
            
            $this->info('PPDB sekarang terbuka.');
            return 0;
        }

        $this->info('Tidak ada PPDB otomatis yang perlu dibuka saat ini.');
        return 0;
    }
}
