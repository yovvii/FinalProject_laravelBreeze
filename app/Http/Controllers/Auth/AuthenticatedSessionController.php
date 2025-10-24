<?php

namespace App\Http\Controllers\Auth;

use App\Models\Siswa;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Auth\LoginRequest;
use App\Traits\LogsStudentActions;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

/**
 * @mixin \Illuminate\Session\Store
 */

class AuthenticatedSessionController extends Controller
{
    use LogsStudentActions;
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validasi Input
        $request->validate([
            'nisn' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Cari data siswa berdasarkan NISN
        $siswa = Siswa::where('nisn', $request->nisn)->first();
        if (!$siswa) {
            throw ValidationException::withMessages([
                'nisn' => __('NISN tidak terdaftar.'),
            ]);
        }

        // Ambil data user dari relasi siswa
        $user = $siswa->user;
        if (!$user) {
            throw ValidationException::withMessages([
                'nisn' => __('Akun tidak ditemukan.'),
            ]);
        }

        // Verifikasi password
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'nisn' => __('Password salah.'),
            ]);
        }

        if ($user->role !== 'siswa') {
            throw ValidationException::withMessages([
                'nisn' => __('Anda tidak memiliki akses sebagai siswa.'),
            ]);
        }

        // Login user
        Auth::login($user, $request->boolean('remember'));
        $loggedInUser = Auth::user();
        $loggedInUser->load('siswa');
        $request->session()->regenerate();

        Session::flash('first_login', true);

        if ($loggedInUser->siswa && $loggedInUser->siswa->has_completed_steps) {
            // Jika langkah sudah selesai (has_completed_steps == true/1)
            return $this->logAndRedirect('setelah.dashboard.show', 'success', 'Berhasil login! Selamat datang kembali.');
        } else {
            // Jika langkah belum selesai (has_completed_steps == false/0 atau null)
            return $this->logAndRedirect('dashboard', 'success', 'Berhasil login! Silahkan lanjutkan tahap selanjutnya!');
        }
        
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
