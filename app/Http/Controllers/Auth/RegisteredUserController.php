<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Siswa;
use App\Models\AgeLimit;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\TimelineProgress;
use Illuminate\Validation\Rules;
use App\Traits\LogsStudentActions;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    use LogsStudentActions;
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $ageLimit = AgeLimit::firstOrNew(); 
        $minAgeYears = $ageLimit->min_age_years;
        $maxAgeYears = $ageLimit->max_age_years;
        $referenceDate = $ageLimit->reference_date ?? Carbon::now();
        $maxDateOfBirth = Carbon::parse($referenceDate)->subYears($minAgeYears)->subDay()->endOfDay();
        $minDateOfBirth = Carbon::parse($referenceDate)->subYears($maxAgeYears)->startOfDay();

        $minDateDisplay = $minDateOfBirth->isoFormat('D MMMM YYYY'); // Cth: 1 Januari 2009
        $maxDateDisplay = $maxDateOfBirth->isoFormat('D MMMM YYYY');

        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'nisn' => ['required', 'string', 'unique:'.Siswa::class],
                // 'password' => ['required', Rules\Password::defaults()],
                'tanggal_lahir' => ['required', 'date', 'after_or_equal:' . $minDateOfBirth->format('Y-m-d'), 'before_or_equal:' . $maxDateOfBirth->format('Y-m-d'), ],
            ], [
                // Custom pesan error
                'nisn.unique' => 'NISN yang Anda masukkan sudah terdaftar. Silahkan login menggunakan data Anda.',
                'tanggal_lahir.after_or_equal' => "Tanggal lahir tidak valid. Calon siswa harus lahir pada atau setelah $minDateDisplay (usia maksimal $maxAgeYears tahun).",
                'tanggal_lahir.before_or_equal' => "Tanggal lahir tidak valid. Calon siswa harus lahir pada atau sebelum $maxDateDisplay (usia minimal $minAgeYears tahun).",
            ]);

            // Gunakan transaksi agar semua query dijalankan atau tidak sama sekali
            $user = DB::transaction(function () use ($request) {
                
                $tanggal_lahir = Carbon::createFromFormat('Y-m-d', $request->tanggal_lahir)->format('dmY');

                // 2. Buat akun user
                $user = User::create([
                    'name' => $request->name,
                    'email' => null,
                    'password' => Hash::make($tanggal_lahir), 
                    'role' => 'siswa',
                ]);

                // 3. Buat Timeline
                TimelineProgress::create([
                    'user_id' => $user->id,
                    'current_step' => 1,
                ]);

                // 4. Buat data siswa
                Siswa::create([
                    'user_id' => $user->id,
                    'nisn' => $request->nisn,
                    'tanggal_lahir' => $request->tanggal_lahir,
                ]);

                // 5. Buat Notifikasi
                NotificationHistory::create([
                    'user_id' => $user->id,
                    'type' => 'success',
                    'message' => 'Selamat! Akun Anda berhasil dibuat. Silahkan lanjutkan ke proses Login.',
                    'is_read' => false,
                ]);
                
                return $user; // Kembalikan user yang baru dibuat
            });

            // Jika transaksi berhasil, event dipicu
            event(new Registered($user));

            // Redirect Sukses
            return $this->logAndRedirect('landing_page', 'success', 'Pendaftaran berhasil! Silahkan login kembali.');

        } catch (ValidationException $e) {
            // 🔥 MENANGKAP ERROR VALIDASI (NISN atau Tanggal Lahir)
            // Ambil pesan error pertama dari semua field yang gagal
            $firstError = collect($e->errors())->flatten()->first();
            
            // Redirect ke landing_page dengan pesan error kustom yang Anda definisikan
            return $this->logAndRedirect('landing_page', 'error', $firstError);
            
        } catch (\Exception $e) {
            // Jika ada error saat proses DB (setelah validasi lolos)
            // dd($e->getMessage());
            
            // Redirect Error (Sesuai permintaan Anda)
            return redirect()->route('landing_page')->with('error', 'Pendaftaran gagal, . Silakan coba lagi.');
        }
    }
}
