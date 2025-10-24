<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Banner;
use App\Models\DataSma;
use App\Models\AgeLimit;
use App\Jobs\OpenSpmbJob;
use App\Models\Akreditasi;
use App\Models\SpmbStatus;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Models\JalurPendaftaran;
use App\Traits\LogsStudentActions;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Jobs\CloseSpmbJob;

class SuperAdminController extends Controller
{
    use LogsStudentActions;
    private const KUOTA_PERSEN = [
        'prestasi' => 0.15, // 15% (Jalur 1)
        'afirmasi' => 0.25, // 25% (Jalur 2)
        'domisili' => 0.60, // 60% (Jalur 3 / Zonasi)
    ];

    public function showLoginForm()
    {
        return view('super_admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Arahkan pengguna berdasarkan peran mereka
            $user = Auth::user();
            if ($user->role === 'super_admin') {
                return redirect()->route('super_admin.dashboard');
            } elseif ($user->role === 'admin_sekolah') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function dashboard()
    {
        $total_sekolah = DataSma::count();
        $total_admin = User::where('role', 'admin_sekolah')->count();
        $total_siswa = Siswa::count();

        return view('super_admin.dashboard', compact('total_sekolah', 'total_admin', 'total_siswa'));
    }

    public function dataSma()
    {
        $smas = DataSma::all();
        return view('super_admin.data_sma', compact('smas'));
    }

    public function createSma()
    {
        $akreditasis = Akreditasi::all();
        return view('super_admin.form_sma', compact('akreditasis'));
    }

    public function storeSma(Request $request)
    {
        $validated = $request->validate([
            'nama_sma' => 'required|string|max:255|unique:sma_datas,nama_sma',
            'akreditasi_id' => 'required|exists:akreditasis,id',
            'kuota_siswa' => 'required|integer|min:0',
            'logo_sma' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]); 

        $logoPath = null;
        if ($request->hasFile('logo_sma')) {
            $logoPath = $request->file('logo_sma')->store('assets/profile_sekolah_jpg', 'public');
        }

        DataSma::create([
            'nama_sma' => $validated['nama_sma'],
            'akreditasi_id' => $validated['akreditasi_id'],
            'kuota_siswa' => $validated['kuota_siswa'],
            'logo_sma' => $logoPath,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return redirect()->route('super_admin.data_sma')->with('success', 'Data sekolah berhasil ditambahkan.');
    }

    public function editSma(DataSma $sma)
    {
        $akreditasis = Akreditasi::all();
        if ($sma->siswas()->count() > 0) {
            return redirect()->route('super_admin.data_sma')->with('error', 'Data tidak bisa diedit! Sekolah ini memiliki ' . $sma->siswas()->count() . ' siswa terdaftar. Hapus data siswa terlebih dahulu');
        }
        return view('super_admin.form_sma', compact('sma', 'akreditasis'));
    }

    public function updateSma(Request $request, DataSma $sma)
    {
        $validated = $request->validate([
            'nama_sma' => 'required|string|max:255|unique:sma_datas,nama_sma,' . $sma->id,
            'akreditasi_id' => 'required|exists:akreditasis,id',
            'logo_sma' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kuota_siswa' => 'required|integer|min:0', 
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($request->hasFile('logo_sma')) {
            if ($sma->logo_sma) {
                Storage::disk('public')->delete($sma->logo_sma);
            }
            $validated['logo_sma'] = $request->file('logo_sma')->store('assets/profile_sekolah_jpg', 'public');
        }

        $sma->update($validated);

        return redirect()->route('super_admin.data_sma')->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function destroySma(DataSma $sma)
    {
        if ($sma->siswas()->count() > 0) {
            return redirect()->route('super_admin.data_sma')->with('error', 'Gagal Menghapus! Sekolah ini memiliki ' . $sma->siswas()->count() . ' siswa terdaftar. Hapus data siswa terlebih dahulu');
        }

        if ($sma->logo_sma) {
            Storage::disk('public')->delete($sma->logo_sma);
        }
        $sma->delete();
        return redirect()->route('super_admin.data_sma')->with('success', 'Data sekolah berhasil dihapus.');
    }

    public function dataAdminSekolah()
    {
        $admins = User::where('role', 'admin_sekolah')->with('sma')->get();
        return view('super_admin.data_admin_sma', compact('admins'));
    }

    public function createAdminForm()
    {
        $schools = DataSma::doesntHave('admin')->get();
        return view('super_admin.create_admin', compact('schools'));
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'sma_data_id' => [
                'required',
                'exists:sma_datas,id',
                Rule::unique('users')->where(fn ($query) => $query->where('role', 'admin_sekolah'))
            ],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin_sekolah',
            'sma_data_id' => $request->sma_data_id,
        ]);

        return redirect()->route('super_admin.dashboard')->with('success', 'Admin sekolah berhasil dibuat.');
    }

    public function editAdminForm(User $admin)
    {
        if ($admin->role !== 'admin_sekolah') {
            return redirect()->route('super_admin.data_admin_sekolah')->with('error', 'Pengguna bukan admin sekolah.');
        }

        $schools = DataSma::doesntHave('admin')
            ->orWhere('id', $admin->sma_data_id)
            ->get();

        return view('super_admin.edit_admin_sma', compact('admin', 'schools'));
    }

    public function updateAdmin(Request $request, User $admin)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'sma_data_id' => [
                'required',
                'exists:sma_datas,id',
                Rule::unique('users')->ignore($admin->id)->where(fn ($query) => $query->where('role', 'admin_sekolah'))
            ],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'sma_data_id' => $validated['sma_data_id'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $admin->update($data);

        return redirect()->route('super_admin.data_admin_sekolah')->with('success', 'Data admin sekolah berhasil diperbarui.');
    }

    public function destroyAdmin(User $admin)
    {
        if ($admin->role !== 'admin_sekolah') {
            return redirect()->route('super_admin.data_admin_sekolah')->with('error', 'Hanya admin sekolah yang bisa dihapus.');
        }

        $admin->delete();
        return redirect()->route('super_admin.data_admin_sekolah')->with('success', 'Admin sekolah berhasil dihapus.');
    }

    public function stopSpmbAndDetermineAcceptance()
    {
        try {
            DB::transaction(function () {

                SpmbStatus::query()->updateOrCreate([], ['status' => 'closed']);
                
                $smas = DataSma::all();

                $acceptedCount = 0;
                $rejectedCount = 0;
                
                foreach ($smas as $sma) {
                    $dayaTampungTotal = $sma->kuota_siswa;
                    
                    if ($dayaTampungTotal <= 0) {
                        Log::warning("SPMB: SMA '{$sma->nama_sma}' dilewati karena Daya Tampung Total ({$dayaTampungTotal}) <= 0.");
                        continue; 
                    }
                    // Asumsi: Kuota jalur disimpan di model DataSma atau model terpisah
                    $kuotaPrestasi = (int) floor(self::KUOTA_PERSEN['prestasi'] * $dayaTampungTotal);
                    $kuotaAfirmasi = (int) floor(self::KUOTA_PERSEN['afirmasi'] * $dayaTampungTotal);
                    $kuotaDomisili = (int) floor(self::KUOTA_PERSEN['domisili'] * $dayaTampungTotal);

                    // Alokasi sisa kuota (karena pembulatan floor) ke jalur Domisili
                    $kuotaTerpakai = $kuotaPrestasi + $kuotaAfirmasi + $kuotaDomisili;
                    $kuotaDomisili += ($dayaTampungTotal - $kuotaTerpakai);
                    
                    Log::info("SPMB: SMA '{$sma->nama_sma}'. Total: {$dayaTampungTotal}. Kuota: P({$kuotaPrestasi}), A({$kuotaAfirmasi}), D({$kuotaDomisili}).");
                   
                    // Proses Penerimaan Per Jalur
                    $this->_processJalur($sma, 1, $kuotaPrestasi); // 1 = Prestasi
                    $this->_processJalur($sma, 2, $kuotaAfirmasi);  // 2 = Afirmasi
                    $this->_processJalur($sma, 3, $kuotaDomisili);   // 3 = Zonasi
                }

                $allStudents = Siswa::with('user', 'dataSma', 'jalurPendaftaran')
                                ->whereNotNull('status_penerimaan') 
                                ->get();
                                
                $totalStudents = $allStudents->count();

                foreach ($allStudents as $siswa) {
                    if (!$siswa->user) continue; 

                    $finalStatus = strtolower($siswa->status_penerimaan);
                    $sekolahTujuan = $siswa->dataSma->nama_sma ?? 'Sekolah Tujuan';
                    $jalur = $siswa->jalurPendaftaran->nama_jalur_pendaftaran ?? 'Pendaftaran';

                    if ($finalStatus === 'diterima') {
                        $message = "🎉 Selamat! Anda dinyatakan DITERIMA di {$sekolahTujuan} melalui Jalur {$jalur}. Silakan segera cek informasi lebih lanjut.";
                        $notificationType = 'success';
                        $acceptedCount++;
                    } else {
                        $message = "😔 Mohon Maaf. Anda dinyatakan DITOLAK di {$sekolahTujuan}. Hasil SPMB telah dirilis. Anda dapat mencari opsi sekolah lain.";
                        $notificationType = 'error';
                        $rejectedCount++;
                    }

                    // Simpan History Notifikasi (Log Siswa)
                    NotificationHistory::create([
                        'user_id' => $siswa->user_id,
                        'type' => $notificationType,
                        'message' => $message,
                        'is_read' => false,
                    ]);
                }

                $adminMessage = "Proses Seleksi SPMB berhasil Dihentikan. Hasil dirilis untuk {$totalStudents} siswa (Diterima: {$acceptedCount}, Ditolak: {$rejectedCount}).";
                NotificationHistory::create([
                    'user_id' => Auth::id(), 
                    'type' => 'info',
                    'message' => $adminMessage,
                    'is_read' => false,
                ]);

            });
            
            return back()->with('success', 'Proses PPDB berhasil dihentikan. Penentuan siswa diterima telah selesai.');


        } catch (\Exception $e) {
            // Log the error
            // Pastikan Anda menggunakan Log::error (tanpa backslash jika sudah di-use)
            Log::error("SPMB Stop Error: " . $e->getMessage()); // <--- PERBAIKI DAN GANTI KE SPMB
                NotificationHistory::create([
                'user_id' => Auth::id(), 
                'type' => 'error',
                'message' => 'Gagal menghentikan proses PPDB/SPMB! Silakan cek log server. Error: ' . Str::limit($e->getMessage(), 100),
                'is_read' => false,
            ]);
            return back()->with('error', 'Gagal menghentikan proses SPMB. Silakan cek log server.'); // <--- GANTI KE SPMB
        }
    }

    public function stopStart()
    {
        $spmbStatus = SpmbStatus::first();
        
        // Cek jika data status ada, jika tidak, inisialisasi null.
        $startingTime = $spmbStatus ? $spmbStatus->starting_at : null; 
        $closingTime = $spmbStatus ? $spmbStatus->closing_at : null;
        $startingTimeIso = $startingTime ? $startingTime->toIso8601String() : null;
        $closingTimeIso = $closingTime ? $closingTime->toIso8601String() : null;
        
        // Anda juga perlu variabel lain yang digunakan di view
        $isClosed = $spmbStatus && $spmbStatus->status === 'closed';
        
        return view('super_admin.start_stop', compact(
            'spmbStatus', 
            'isClosed', 
            'startingTime', // 🔥 Pastikan ini dikirim
            'closingTime',   // 🔥 Pastikan ini dikirim
            'startingTimeIso', // 🔥 Kirim ke View
            'closingTimeIso'
        ));
    }

    public function setSchedule(Request $request)
    {
        $validatedData = $request->validate([
            'starting_at' => 'required|date|after:now', // Harus di masa depan
            'closing_at' => 'required|date|after:starting_at',
        ]);

        $status = SpmbStatus::firstOrNew();
        $status->starting_at = $validatedData['starting_at'];
        $status->closing_at = $validatedData['closing_at'];
        $status->save();
        
        // --- LOGIKA DISPATCH JOB ---

        // PENTING: Jika ada cara untuk membatalkan Job lama (seperti dengan Job Batching/Tags), lakukan di sini.
        // Untuk sederhana, kita akan biarkan Job lama (jika ada) terlewati jika status berubah.
        
        $startingTime = Carbon::parse($status->starting_at);
        $closingTime = Carbon::parse($status->closing_at);

        // 1. Dispatch Job Pembuka (ditunda sampai starting_at)
        OpenSpmbJob::dispatch()->delay($startingTime);

        // 2. Dispatch Job Penutup (ditunda sampai closing_at)
        CloseSpmbJob::dispatch()->delay($closingTime);
        
        // Atur status awal ke pending/closed agar Job pembuka bisa berjalan
        if ($status->status === 'open' && $startingTime->isFuture()) {
             $status->status = 'closed'; // Atau 'pending', agar Job Open bisa jalan
             $status->save();
        }

        return back()->with('success', 'Jadwal otomatis PPDB berhasil diperbarui.');
    }

    private function _processJalur($sma, $jalurId, $kuota)
    {
        // 1. Ambil query yang sudah diurutkan (sama seperti di AdminSekolahController)
        $query = Siswa::where('data_sma_id', $sma->id)
                    ->where('jalur_pendaftaran_id', $jalurId)
                    ->where('status_pendaftaran', 'completed'); // Hanya proses yang sudah final

        if ($jalurId == 1) { // Prestasi
            // Prioritas: Verifikasi Sertifikat (Terverifikasi/Belum) > Nilai Akhir > Tanggal Lahir
            $query->orderBy(DB::raw("CASE 
                WHEN verifikasi_sertifikat = 'terverifikasi' THEN 1 
                ELSE 0 
            END"), 'desc')
            ->orderByDesc('nilai_akhir')
            ->orderBy('tanggal_lahir', 'asc');
        } elseif ($jalurId == 2) { // Afirmasi
            // Prioritas: Verifikasi Afirmasi (Terverifikasi) > Jarak > Tanggal Lahir
            $query->where('verifikasi_afirmasi', 'terverifikasi')
                ->orderBy('jarak_ke_sma_km')
                ->orderBy('tanggal_lahir', 'asc');
        } elseif ($jalurId == 3) { // Zonasi
            // Prioritas: Jarak > Tanggal Lahir
            $query->orderBy('jarak_ke_sma_km')
                ->orderBy('tanggal_lahir', 'asc');
        } else {
            return; // Lewati jalur lain yang mungkin tidak relevan
        }
        
        // 2. Ambil data yang sudah diurutkan
        $siswas = $query->get();

        // 3. Update Status
        foreach ($siswas as $index => $siswa) {
            if ($index < $kuota) {
                // Diterima
                $siswa->status_penerimaan = 'diterima';
            } else {
                // Ditolak
                $siswa->status_penerimaan = 'ditolak';
            }
            $siswa->save();
        }
    }

    public function showAcceptedStudents(Request $request)
    {
        // 1. Ambil Status PPDB Global dari model PpdbStatus
        // Gunakan first() dan berikan nilai default jika tabel masih kosong.
        $spmb_status = SpmbStatus::query()->first()->status ?? 'active';
        $smas = DataSma::all();
        $jalurs = JalurPendaftaran::all();
        $filterSmaId = $request->query('sma_id');
        $filterJalurId = $request->query('jalur_id');

        $siswasDiterima = collect();

        // 2. Jika proses sudah dihentikan ('closed'), baru ambil data yang diterima
        if ($spmb_status === 'closed') {
            $siswasDiterima = Siswa::with('user', 'dataSma', 'jalurPendaftaran')
                                ->where('status_penerimaan', 'diterima');
            if ($filterSmaId) {
                $siswasDiterima->where('data_sma_id', $filterSmaId);
            }

            // Terapkan Filter Jalur
            if ($filterJalurId) {
                $siswasDiterima->where('jalur_pendaftaran_id', $filterJalurId);
            }

            // Ambil data yang sudah difilter dan tambahkan parameter query ke link pagination
            $siswasDiterima = $siswasDiterima->paginate(50)->appends($request->query());
        }
        
        return view('super_admin.data_diterima', compact('spmb_status', 'siswasDiterima', 'smas', 'jalurs', 'filterSmaId', 'filterJalurId'));
    }

    public function resetSpmbStatus()
    {
        try {
            DB::transaction(function () {
                // 1. Ubah status global kembali menjadi 'active'
                SpmbStatus::query()->updateOrCreate([], ['status' => 'active']);

                $spmbStatus = SpmbStatus::query()->first();
            
                if ($spmbStatus) {
                    // Jika baris sudah ada (seharusnya ada setelah di-stop), update statusnya
                    $spmbStatus->status = 'active';
                    $spmbStatus->save();
                } else {
                    // Jika tabel benar-benar kosong (kasus jarang), buat baris baru
                    SpmbStatus::create(['status' => 'active']);
                }

                // 2. Reset status penerimaan semua siswa
                // Pastikan Anda hanya mereset siswa yang status pendaftarannya completed
                Siswa::whereIn('status_penerimaan', ['diterima', 'ditolak'])
                    ->update(['status_penerimaan' => null]);
                
                Siswa::where('result_viewed', true)
                 ->update(['result_viewed' => false]);
            });

            return back()->with('success', 'Proses SPMB berhasil diatur ulang dan dimulai kembali.');

        } catch (\Exception $e) {
            Log::error("SPMB Reset Error: " . $e->getMessage());
            return back()->with('error', 'Gagal mengatur ulang proses SPMB. Cek log server.');
        }
    }

    public function indexBanner()
    {
        $banners = Banner::orderBy('id', 'desc')->get();
        return view('super_admin.banner', compact('banners'));
    }

    public function storeBanner(Request $request) // 🔥 Nama fungsi diubah menjadi storeBanner
    {
        // 1. Validasi File Upload
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
            'content' => 'nullable|string|max:500', 
        ]);

        // 2. Proses File Upload
        $path = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // Simpan file baru di storage/app/public/banners
            $path = $file->store('banners', 'public'); 
        }
        
        // 3. 🔥 Buat record baru di database (CREATE)
        $banner = Banner::create([
            'image_path' => $path,
            'content' => $request->input('content'), 
            'is_active' => true, // Default aktif saat dibuat
            // Tambahkan kolom lain jika ada, seperti 'urutan' atau 'link'
        ]);

        // 4. Redirect kembali ke halaman index banner
        return redirect()->route('banner.index')->with('success', 'Banner baru berhasil ditambahkan.');
    }

    public function addBanner()
    {
        $banner = Banner::firstOrNew([]); 
        return view('super_admin.banner', compact('banner')); 
    }

    public function deleteBanner(Banner $banner)
    {
        // 1. Hapus file fisik dari storage
        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }
        
        // 2. Hapus record dari database
        $banner->delete();

        return redirect()->route('banner.index')->with('success', 'Banner berhasil dihapus.');
    }

    public function indexUsia()
    {
        $ageLimit = AgeLimit::firstOrNew([], [
            'min_age_years' => 14, 
            'max_age_years' => 17,
            // Atur default reference_date ke 1 Januari tahun ini
            'reference_date' => Carbon::create(Carbon::now()->year, 1, 1), 
        ]);

        return view('super_admin.usia_siswa', compact('ageLimit'));
    }

    public function updateUsia(Request $request)
    {
        $request->validate([
            'min_age_years' => 'required|integer|min:1',
            'max_age_years' => 'required|integer|gte:min_age_years',
            'reference_date' => 'required|date',
        ]);

        // Gunakan updateOrCreate karena hanya akan ada 1 baris
        AgeLimit::updateOrCreate([], [
            'min_age_years' => $request->min_age_years,
            'max_age_years' => $request->max_age_years,
            'reference_date' => $request->reference_date,
        ]);

        return back()->with('success', 'Batas usia siswa berhasil diperbarui.');
    }

    public function showInformasi()
    {
        // Ambil data, atau buat baru jika belum ada (hanya ada 1 baris)
        $setting = GlobalSetting::firstOrNew();

        return view('super_admin.informasi', compact('setting'));
    }

    public function updateInformasi(Request $request)
    {
        $request->validate([
            'important_info_content' => 'nullable|string',
            'juknis_pdf' => 'nullable|file|mimes:pdf|max:5120',
            'alur_pendaftaran' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Menggunakan updateOrCreate karena kita hanya ingin satu baris data
        $setting = GlobalSetting::firstOrNew(['id' => 1]);
        $data = [
            'important_info_content' => $request->important_info_content,
        ];
        
        // 3. Proses File Upload
        if ($request->hasFile('juknis_pdf')) {
            $file = $request->file('juknis_pdf');
            
            // Hapus file lama jika ada
            if ($setting->juknis_pdf_path) {
                Storage::disk('public')->delete($setting->juknis_pdf_path);
            }
            
            // Simpan file baru di folder 'public/juknis'
            // 'juknis' adalah nama folder di dalam direktori storage/app/public
            $path = $file->store('juknis', 'public'); 
            $data['juknis_pdf_path'] = $path;
        }

        if ($request->hasFile('alur_pendaftaran')) {
            $file = $request->file('alur_pendaftaran');
            
            // Hapus file lama jika ada
            if ($setting->alur_pendaftaran_path) {
                Storage::disk('public')->delete($setting->alur_pendaftaran_path);
            }
            
            // Simpan file baru di folder 'alur-pendaftaran'
            $path = $file->store('alur-pendaftaran', 'public'); 
            $data['alur_pendaftaran_path'] = $path;
        }

        // 4. Update atau Simpan ke Database
        $setting->fill($data)->save();

        return back()->with('success', 'Informasi, Juknis, dan Alur Pendaftaran berhasil diperbarui.');
    }

    
}
