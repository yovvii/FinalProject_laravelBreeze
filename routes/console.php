<?php

use App\Models\SpmbStatus;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $status = SpmbStatus::firstOrNew();

    if ($status->status !== 'open' && $status->starting_at && Carbon::now()->greaterThanOrEqualTo($status->starting_at)) {
        $status->status = 'open';
        $status->save();
    }
})->everyMinute()->name('ppdb_open_check');

// Penjadwalan untuk menutup PPDB
Schedule::call(function () {
    $status = SpmbStatus::first();

    if ($status && $status->status === 'open' && $status->closing_at && Carbon::now()->greaterThanOrEqualTo($status->closing_at)) {
        
        $status->status = 'closed';
        $status->save();
    }
})->everyMinute()->name('ppdb_close_check');
