<?php

namespace App\Jobs;

use App\Models\SpmbStatus;
use Illuminate\Support\Facades\Log; // Gunakan Log::info() untuk logging yang benar
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Carbon\Carbon;

class CloseSpmbJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $status = SpmbStatus::first();

        // Cek apakah waktu penutupan sudah tercapai
        if ($status && $status->status === 'open' && $status->closing_at && Carbon::now()->greaterThanOrEqualTo($status->closing_at)) {
            
            // 🔥 Logika penentuan penerimaan dihapus

            $status->status = 'closed';
            $status->save();

            // 🔥 Logika ini akan dicatat di log saat Job Selesai
            Log::info('Waktu penutupan PPDB otomatis telah tercapai. Status global diubah menjadi CLOSED.');
        } else {
            // Log::info('Tidak ada PPDB otomatis yang perlu ditutup saat ini.');
        }
    }
}