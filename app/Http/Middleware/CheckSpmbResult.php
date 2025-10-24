<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\SpmbStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSpmbResult
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'siswa') {
            
            $user = Auth::user();
            $user->load('siswa');
            $siswa = $user->siswa;
            
            // 1. Cek Status Seleksi SPMB
            $spmbStatus = SpmbStatus::first();
            $selection_ended = $spmbStatus && $spmbStatus->status === 'closed';

            // 2. Jika SPMB sudah ditutup dan siswa ada
            if ($selection_ended && $siswa) {
                $statusPenerimaan = strtolower($siswa->status_penerimaan ?? '');
                
                $resultDetermined = ($statusPenerimaan === 'diterima' || $statusPenerimaan === 'ditolak' || $statusPenerimaan === 'tidak diterima');
                
                if ($resultDetermined && !$siswa->result_viewed) {
                    session()->flash('spmb_result_status', $statusPenerimaan);
                    $siswa->result_viewed = true;
                    $siswa->save();
                }
            }
        }
        return $next($request);
    }
}
