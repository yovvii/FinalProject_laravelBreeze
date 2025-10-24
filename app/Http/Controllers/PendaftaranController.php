<?php

namespace App\Http\Controllers;

use App\Models\Ortu;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\Banner;
use App\Models\Semester;
use App\Models\RaporFile;
use App\Models\SpmbStatus;
use App\Models\SekolahAsal;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Traits\LogsStudentActions;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PendaftaranController extends Controller
{
    use LogsStudentActions;
    
    public function showTimeline(Request $request, $pathStep = null)
    {
        $progress = Auth::user()->timelineProgress->current_step ?? 1;
        $currentStep = $pathStep ?? $request->query('step', $progress);
        $currentStep = (int) $currentStep;
        if ($currentStep > $progress) {
            $currentStep = $progress;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user()->load('siswa.ortu', 'semesters', 'raporFiles');
        $siswa = $user->siswa;
        $ortu  = $siswa->ortu ?? null;
        $sekolahAsals = SekolahAsal::all();
        $mapels = Mapel::all();
        $isPasswordChanged = Auth::user()->password_changed_at;
        
        $tanggal_lahir_formatted = null;
        if ($siswa && $siswa->tanggal_lahir) {
            $tanggal_lahir_formatted = Carbon::parse($siswa->tanggal_lahir)->format('dmY');
        }

        $raporData = [];
        for ($semester = 1; $semester <= 5; $semester++) {
            $raporData[$semester] = [
                'file_rapor' => $user->raporFiles->firstWhere('semester', $semester),
            ];
        }

        if ($siswa && ($siswa->has_completed_steps)) {
            NotificationHistory::create([
                'user_id' => $user->id,
                'type' => 'success',
                'message' => 'Proses pembuatan akun telah selesai!',
                'is_read' => false,
            ]);
            return redirect()->route('setelah.dashboard.show');
        }

        return view('dashboard', [
            'currentStep' => (int)$currentStep,
            'siswa' => $siswa,
            'ortu' => $ortu,
            'sekolahAsals' => $sekolahAsals,
            'isPasswordChanged' => $isPasswordChanged,
            'tanggal_lahir_formatted' => $tanggal_lahir_formatted,

            'mapels' => $mapels,
            'semesters' => $user->semesters,
            'raporData' => $raporData,
            'raporFiles' => $user->raporFiles,
        ]);

    }

    public function saveRegistration(Request $request)
    {
        $step = $request->input('current_step');

        try {
            switch ($step) {
                case 1:
                    $this->_saveBiodata($request);
                    break;
                case 2:
                    $this->_saveRapor($request);
                    break;
                case 3:
                    $this->_saveSuratPernyataan($request);
                    break;
                case 4:
                    $this->_saveSuratKeteranganLulus($request);
                    break;
            }
        } catch (\Exception $e) {
            // Ini adalah blok untuk error selain validasi (seperti error DB yang tak terduga)
            
            // Log error untuk debug
            \Illuminate\Support\Facades\Log::error("Error saving registration step {$step}: " . $e->getMessage());

            // Redirect dengan pesan error umum jika terjadi error sistem/DB
            // Karena ini bukan error validasi, kita gunakan 'error' flash session.
            return redirect()->back()->with('error', 'Gagal menyimpan data karena kesalahan sistem tak terduga. Silakan coba lagi.');
        }

        $progress = Auth::user()->timelineProgress;
        $nextStep = (int)$step + 1;

        if ($nextStep > $progress->current_step) {
            $progress->current_step = $nextStep;
            $progress->save();
        }

        return redirect()->route('dashboard', ['step' => $nextStep]);
    }

    private function _saveBiodata(Request $request)
    {
        $validatedData = $request->validate([
            // file
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'akta_file' => 'nullable|file|mimes:pdf|max:2048',

            // data user
            'name' => 'required|string|max:255',
            'email' => [
                'nullable', 
                'string', 
                'email', 
                'max:255',
                // 🔥 ATURAN BARU: Unik di tabel 'users', kecuali user yang sedang login 🔥
                Rule::unique('users', 'email')->ignore(Auth::id()),
            ],

            // data siswa
            'jenis_kelamin' => 'nullable|string',
            'kabupaten' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'desa' => 'nullable|string',
            'alamat' => 'nullable|string',
            'no_kk' => 'nullable|string',
            'nik' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'nama_ayah' => 'nullable|string',
            'nama_ibu' => 'nullable|string',
            'email' => 'nullable|string',
            'agama' => 'nullable|string',
            'kebutuhan_k' => 'nullable|string',
            'sekolah_asal_id' => 'nullable|integer',
            'latitude' => 'required|numeric', 
            'longitude' => 'required|numeric',
            
            // data wali
            'nama_wali' => 'nullable|string|max:255',
            'tempat_lahir_wali' => 'nullable|string|max:255',
            'tanggal_lahir_wali' => 'nullable|string|max:255',
            'pekerjaan_wali' => 'nullable|string|max:255',
            'alamat_wali' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validatedData, $request) {
            $user = Auth::user();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->save();

            $siswa = $user->siswa;
            $siswa = $user->siswa ?? new Siswa();
            $siswa->user_id = $user->id;

            if ($request->hasFile('foto')) {
                if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
                    Storage::disk('public')->delete($siswa->foto);
                }
                $siswa->foto = $request->file('foto')->store('profile_murid', 'public');
            }
            if ($request->hasFile('akta_file')) {
                // 1. Dapatkan file yang diupload
                $file = $request->file('akta_file');
                
                // 2. Jika ada file lama, hapus dulu
                if ($siswa->akta_file && Storage::disk('public')->exists($siswa->akta_file)) {
                    Storage::disk('public')->delete($siswa->akta_file);
                }
                
                // 3. Simpan file dan dapatkan path yang benar
                $pathAkta = $file->store('akta_murid', 'public');

                // 4. Update model dengan path yang sudah terverifikasi
                $siswa->akta_file = $pathAkta;
            }
            
            $siswaData = collect($validatedData)->except([
                'nama_wali', 'tempat_lahir_wali', 'tanggal_lahir_wali', 'pekerjaan_wali', 'alamat_wali', 'foto', 'akta', 'email'
            ])->toArray();

            // dd($validatedData);

            $siswa->fill($siswaData);
            $siswa->latitude_siswa = $validatedData['latitude'];
            $siswa->longitude_siswa = $validatedData['longitude'];
            $siswa->save();

            NotificationHistory::create([
                'user_id' => $user->id,
                'type' => 'success',
                'message' => 'Biodata berhasil disimpan',
                'is_read' => false,
            ]);

            $ortuData = $request->only([
                'nama_wali', 'tempat_lahir_wali', 'tanggal_lahir_wali', 'pekerjaan_wali', 'alamat_wali'
            ]);

            Ortu::updateOrCreate(
                ['siswa_id' => $siswa->id],
                $ortuData
            );            
        });

        return redirect()->route('dashboard')->with('success', 'Biodata berhasil disimpan!');
    }
    
    private function _saveRapor(Request $request)
    {
        try {
            $userId = Auth::id();
            $dataNilai = $request->input('nilai', []);
            
            // 1. Ambil file rapor yang sudah ada di database untuk user ini
            $existingRaporFiles = RaporFile::where('user_id', $userId)
                                        ->pluck('file_rapor', 'semester');

            // 2. Tentukan aturan dasar untuk file dan non-file
            $baseRules = [
                'nilai' => 'required|array|max:100',
                'rapor_file' => 'nullable|array',
            ];
            $fileBaseRules = 'mimes:pdf|max:1000'; // Aturan untuk tipe dan ukuran file

            // 3. Bangun aturan validasi file secara dinamis
            foreach (array_keys($dataNilai) as $semester) {
                $fileKey = "rapor_file.{$semester}";
                
                // Cek apakah file untuk semester ini sudah ada
                if ($existingRaporFiles->has((int)$semester)) {
                    // Jika sudah ada, upload file baru bersifat opsional
                    $baseRules[$fileKey] = 'nullable|' . $fileBaseRules;
                } else {
                    // Jika belum ada, upload file baru wajib (required)
                    $baseRules[$fileKey] = 'required|' . $fileBaseRules;
                }
            }

            // 4. Lakukan validasi dengan aturan yang telah dibuat
            $validated = $request->validate($baseRules);

            // --- Logika Penyimpanan Data (Tetap sama seperti yang Anda miliki) ---
            
            DB::beginTransaction();

            $dataNilai = $validated['nilai'];
            $mapels = Mapel::all();
            // $rapor_file = RaporFile::all(); // Baris ini tidak diperlukan di sini

            foreach ($dataNilai as $semester => $nilaiMapel) {
                if ($request->hasFile("rapor_file.{$semester}")) {
                    $file = $request->file("rapor_file.{$semester}");

                    $existingRaporFile = RaporFile::where('user_id', $userId)->where('semester', $semester)->first();

                    // Logika menghapus file lama
                    if ($existingRaporFile && Storage::disk('public')->exists($existingRaporFile->file_rapor)) {
                        // Catatan: Jika $existingRaporFile->file_rapor hanya berisi nama file, 
                        // Anda mungkin perlu menyesuaikan path di sini agar benar:
                        // Storage::disk('public')->delete('rapor_murid/' . $userId . '/' . $existingRaporFile->file_rapor);
                        Storage::disk('public')->delete($existingRaporFile->file_rapor); // Mengikuti asumsi kode Anda
                    }
                    
                    $fileName = $file->hashName('rapor_murid/' . $userId);
                    $file->storeAs('rapor_murid/' . $userId, $fileName, 'public');

                    RaporFile::updateOrCreate(
                        ['user_id' => $userId,
                        'semester' => $semester],
                        ['file_rapor' => $fileName]
                    );
                }
                
                // ... (Logika penyimpanan nilai semester) ...
                foreach ($nilaiMapel as $namaMapel => $nilai) {
                    $namaMapelDariForm = Str::replace('_', ' ', $namaMapel);

                    $mapelId = $mapels->firstWhere(function ($mapel) use ($namaMapelDariForm) {
                        return Str::lower($mapel->nama_mapel) === Str::lower($namaMapelDariForm);
                    })->id ?? null;

                    if ($nilai !== null && $mapelId !== null) {
                        Semester::updateOrCreate(
                            ['user_id' => $userId,
                            'mapel_id' => $mapelId,
                            'semester' => $semester],
                            ['nilai_semester' => $nilai]
                        );
                    }
                }
            }

            NotificationHistory::create([
                'user_id' => $userId,
                'type' => 'success',
                'message' => 'Data rapor berhasil disimpan',
                'is_read' => false,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Data rapor berhasil disimpan!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan rapor: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    public function _saveSuratPernyataan(Request $request)
    {
        $validated = $request->validate([
            'surat_pernyataan' => 'nullable|file|mimes:pdf|max:2048',
            ]);

        DB::transaction(function () use ($validated, $request) {
            $user = Auth::user();
            $siswa = $user->siswa;

            if ($request->hasFile('surat_pernyataan')) {
                if ($siswa->surat_pernyataan && Storage::disk('public')->exists($siswa->surat_pernyataan)) {
                    Storage::disk('public')->delete($siswa->surat_pernyataan);
                }
                $siswa->surat_pernyataan = $request->file('surat_pernyataan')->store('surat_pernyataan', 'public');
                $siswa->save();

                NotificationHistory::create([
                    'user_id' => $user->id,
                    'type' => 'success',
                    'message' => 'Surat Pernyataan berhasil diupload!',
                    'is_read' => false,
                ]);
            }
        });

        return redirect()->route('dashboard')->with('success', 'Surat Pernyataan berhasil diupload!');

    }
    
    public function _saveSuratKeteranganLulus(Request $request)
    {
        $validated = $request->validate([
            'surat_keterangan_lulus' => 'nullable|file|mimes:pdf|max:2048',
            'ijazah_file' => 'nullable|file|mimes:pdf|max:2048',
            ]);

        DB::transaction(function () use ($validated, $request) {
            $user = Auth::user();
            $siswa = $user->siswa;

            if ($request->hasFile('surat_keterangan_lulus')) {
                if ($siswa->surat_keterangan_lulus && Storage::disk('public')->exists($siswa->surat_keterangan_lulus)) {
                    Storage::disk('public')->delete($siswa->surat_keterangan_lulus);
                }
                $siswa->surat_keterangan_lulus = $request->file('surat_keterangan_lulus')->store('surat_keterangan_lulus', 'public');
                $siswa->save();
            }
            if ($request->hasFile('ijazah_file')) {
                if ($siswa->ijazah_file && Storage::disk('public')->exists($siswa->ijazah_file)) {
                    Storage::disk('public')->delete($siswa->ijazah_file);
                }
                $siswa->ijazah_file = $request->file('ijazah_file')->store('ijazah_file', 'public');
                $siswa->save();
            }
            if ($request->input('current_step') == 4) {
                $siswa->status_pendaftaran_akun = 'completed'; // Tambahkan kolom status baru
                $siswa->has_completed_steps = true;
                $siswa->save();

                NotificationHistory::create([
                    'user_id' => $user->id,
                    'type' => 'success',
                    'message' => 'Surat Keterangan Lulus dan Ijazah berhasil disubmit!',
                    'is_read' => false,
                ]);
            }
        });

        return redirect()->route('dashboard')->with('success', 'Surat Keterangan Lulus dan Ijazah berhasil disubmit!');
    }

    public function showSetelahDashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('siswa.dataSma', 'siswa.jalurPendaftaran');
        $siswa = $user->siswa;

        $spmbStatus = SpmbStatus::first();
        $selection_ended = $spmbStatus && $spmbStatus->status === 'closed';

        // 2. Cek Status Siswa
        // Asumsi: 'status_pendaftaran' di tabel Siswa berisi 'diterima' jika lolos
        $is_accepted = $siswa && $siswa->status_penerimaan === 'diterima';
        
        // 3. Ambil dan Tandai Notifikasi (Kode yang sudah ada)
        $notifications = $user->notifications()
                            ->where('is_read', false)
                            ->orderBy('created_at', 'desc')
                            ->get();

        $notifications = $user->notifications()
                          ->where('is_read', false)
                          ->orderBy('created_at', 'desc')
                          ->get();

        if ($notifications->isNotEmpty()) {
            DB::transaction(function () use ($notifications) {
                NotificationHistory::whereIn('id', $notifications->pluck('id'))
                                ->update(['is_read' => false]);
            });
        }

        $banners = $this->getInformasiBanner();
        $selection_ended = $spmbStatus && $spmbStatus->status === 'closed';

        // Ambil konten informasi
        $globalSetting = GlobalSetting::first();
        $infoContent = $globalSetting ? $globalSetting->important_info_content : 'Informasi penting belum diatur oleh admin.';
        
        return view('setelah_dashboard', compact('siswa', 'notifications', 'selection_ended', 'is_accepted', 'banners', 'selection_ended', 'infoContent'))->with('success', 'Proses Pembuatan Akun Telah Selesai!');
    }

    public function getInformasiBanner()
    {
        // Mengambil banner yang terakhir dibuat (paling baru)
        $banners = Banner::where('is_active', true)->orderBy('id', 'desc')->get(); 
        return $banners; 
    }

    public function juknisPendaftaran()
    {
        $globalSetting = GlobalSetting::first();
        $juknisPath = $globalSetting ? $globalSetting->juknis_pdf_path : null;
        return view('juknis', compact('juknisPath'));
    }
    
    public function alurSpmb()
    {
        $globalSetting = GlobalSetting::first();
        $alurPendaftaranPath = $globalSetting ? $globalSetting->alur_pendaftaran_path : null;
        return view('alur_spmb', compact('alurPendaftaranPath'));
    }
}