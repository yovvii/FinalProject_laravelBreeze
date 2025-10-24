<?php

namespace App\Providers;

use App\Models\DataSma;
use App\Models\SpmbStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('spmb_statuses')) { 
        
            // Gunakan '*' untuk memastikan variabel tersedia di SEMUA views, 
            // termasuk layout utama (app.blade.php)
            View::composer('*', function ($view) {
                
                // Ambil status PPDB dari database
                $ppdb_status = SpmbStatus::first(); 

                // Tentukan apakah seleksi telah berakhir (berdasarkan status 'closed')
                $selection_ended = ($ppdb_status && $ppdb_status->status === 'closed'); 
                
                // Variabel $selection_ended sekarang tersedia di SEMUA view
                $view->with('selection_ended', $selection_ended);

                if (Auth::check() && Auth::user()->role === 'siswa' && $selection_ended) {
                    $siswa = Auth::user()->siswa;
                    
                    // Cek apakah siswa punya hasil seleksi di database dan belum pernah di-flash
                    if ($siswa && $siswa->spmb_result && !session()->has('spmb_result_status')) { 
                        
                        // Jika hasilnya 'diterima' atau 'ditolak', flash ke session
                        // Session ini akan ditangkap oleh JavaScript SweetAlert Anda
                        session()->flash('spmb_result_status', $siswa->spmb_result);

                        // OPSIONAL: Jika Anda ingin alert HANYA muncul sekali seumur hidup siswa
                        // Anda bisa menambahkan kolom 'alert_shown' dan set 'spmb_result' = null
                    }
                }
            });
        }

        if (Schema::hasTable('users')) {
        
            // Atur View Composer untuk view sidebar Anda
            // Ganti 'layouts.sidebar' dengan path view sidebar Anda yang sebenarnya!
            View::composer(['admin_sekolah.layouts.partials.sidebar_admin'], function ($view) {
                
                $data_sma = null;
                $user = Auth::user();

                // 🔥 PERBAIKAN: Cek role 'admin_sekolah'
                if ($user && $user->role === 'admin_sekolah') {
                    
                    // 🔥 PERBAIKAN: Gunakan kolom 'sma_data_id'
                    $smaId = $user->sma_data_id ?? null; 
                    
                    if ($smaId) {
                        $data_sma = DataSma::find($smaId);
                        
                        Log::info("Data SMA ditemukan untuk ID: {$smaId}"); // Opsional: untuk cek log
                    }
                }
                
                // Variabel $data_sma dikirim ke view
                $view->with('data_sma', $data_sma);
            });
        }
    }
}
