<?php

namespace App\Http\Controllers;

use App\Models\Ortu;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\DataSma;
use App\Models\Semester;
use App\Models\RaporFile;
use App\Models\SpmbStatus;
use App\Models\SekolahAsal;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Models\JalurPendaftaran;
use App\Models\TimelineProgress;
use App\Traits\LogsStudentActions;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SmaController extends Controller
{
    private const KUOTA_PERSEN = [
        '1' => 0.15, // Prestasi
        '2' => 0.25, // Afirmasi
        '3' => 0.60, // Domisili/Zonasi
    ];

    use LogsStudentActions;
    /**
     * Tampilkan halaman daftar SMA.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $siswa = $user->siswa;

        if ($siswa && $siswa->data_sma_id) {
            if (!$siswa->jalur_pendaftaran_id) {
                return redirect()->route('jalur_pendaftaran', ['sma_id' => $siswa->data_sma_id]);
            }
            
            if ($siswa->status_pendaftaran === 'completed') {
                return redirect()->route('siswa.peringkat');
            }
        }

        $data_sekolah_sma = DataSma::with('akreditasi')->withCount('siswas')->get();
        return view('pendaftaran_sma', compact('data_sekolah_sma'));
    }

    public function showJalurPendaftaran(Request $request)
    {
        $validated = $request->validate([
            'sma_id' => 'required|integer|exists:sma_datas,id',
        ]);
        
        $sma_id = $validated['sma_id'];
        $user = Auth::user();
        $siswa = $user->siswa;

        if ($siswa && $siswa->data_sma_id !== $sma_id) {
        // Hapus file fisik jika ada
            if ($siswa->sertifikat_file && Storage::disk('public')->exists($siswa->sertifikat_file)) {
                Storage::disk('public')->delete($siswa->sertifikat_file);
            }
            if ($siswa->document_afirmasi && Storage::disk('public')->exists($siswa->document_afirmasi)) {
                Storage::disk('public')->delete($siswa->document_afirmasi);
            }

            // Reset semua field verifikasi dan file ke NULL
            $siswa->update([
                'data_sma_id' => $sma_id, // Update SMA ID
                'sertifikat_file' => null,
                'verifikasi_sertifikat' => 'pending',
                'document_afirmasi' => null,
                'verifikasi_afirmasi' => 'pending',
                // Reset jalur pendaftaran agar siswa diarahkan ke pemilihan jalur
                'jalur_pendaftaran_id' => null, 
                'status_pendaftaran' => 'in_progress', // Reset status pendaftaran
            ]);
        } else if (!$siswa) {
            // Logika untuk siswa yang baru pertama kali mendaftar (jika perlu)
        }

        $jalur_pendaftaran = JalurPendaftaran::all();
        return view('registration.sma_form.jalur_pendaftaran_sma', compact('jalur_pendaftaran', 'sma_id'));
    }

    public function showResume()
    {
        $siswa = Auth::user()->siswa->load(['jalurPendaftaran', 'dataSma', 'semesters.mapels']);
        return view('registration.sma_form.resume_sma', compact('siswa'));
    }

    private function _calculateAndSaveNilaiAkhir()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $siswa = $user->siswa;
        
        // Hitung total semua nilai dan jumlah mata pelajaran yang sudah diisi
        $totalNilai = $user->semesters()->sum('nilai_semester');
        $totalMapel = $user->semesters()->count();

        $nilaiAkhir = 0.00;
        if ($totalMapel > 0) {
            // Hitung rata-rata dan bulatkan ke 2 angka di belakang koma
            $nilaiAkhir = round($totalNilai / $totalMapel, 2); 
        }

        // Simpan nilai akhir ke kolom nilai_akhir di tabel siswa jika ada perubahan
        if ($siswa->nilai_akhir != $nilaiAkhir) {
            $siswa->nilai_akhir = $nilaiAkhir;
            $siswa->save();
        }
    }

    public function saveJalurPendaftaran(Request $request): RedirectResponse
    {
        // dd($request->all());
        $validated = $request->validate([
            'jalur_pendaftaran_id' => [
                'required',
                'integer',
                Rule::exists('jalur_pendaftarans', 'id'),
            ],
            'sma_id' => [
                'required',
                'integer',
                Rule::exists('sma_datas', 'id'),
            ],
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->back()->with('error', 'Pengguna tidak terautentikasi.');
            }

            $siswa = Siswa::firstOrNew(['user_id' => $user->id]);
            $old_jalur_id = $siswa->jalur_pendaftaran_id;
            $old_sma_id = $siswa->data_sma_id;

            $new_jalur_id = $validated['jalur_pendaftaran_id'];
            $new_sma_id = $validated['sma_id'];

            // --- LOGIKA RESET DATA VERIFIKASI ---
            $isJalurChanged = ($siswa->exists && $old_jalur_id !== (int)$new_jalur_id && $old_jalur_id !== null);
            $isSmaChanged = ($siswa->exists && $old_sma_id !== (int)$new_sma_id && $old_sma_id !== null);

            if ($isJalurChanged || $isSmaChanged) {
                
                // 1. Hapus file fisik lama jika ada
                if ($siswa->sertifikat_file && Storage::disk('public')->exists($siswa->sertifikat_file)) {
                    Storage::disk('public')->delete($siswa->sertifikat_file);
                }
                if ($siswa->document_afirmasi && Storage::disk('public')->exists($siswa->document_afirmasi)) {
                    Storage::disk('public')->delete($siswa->document_afirmasi);
                }

                // 2. Reset kolom file ke NULL dan status verifikasi ke 'pending'
                $siswa->sertifikat_file = null;
                $siswa->verifikasi_sertifikat = 'pending'; // Wajib 'pending' (bukan NULL)
                $siswa->document_afirmasi = null;
                $siswa->verifikasi_afirmasi = 'pending';   // Wajib 'pending' (bukan NULL)
                
                // Logika reset lain yang mungkin diperlukan (misalnya nilai rapor/jarak)
                // $siswa->nilai_akhir = null; 
                // $siswa->jarak_ke_sma_km = null;
            }

            $siswa->jalur_pendaftaran_id = $new_jalur_id;
            $siswa->data_sma_id = $new_sma_id;
            // dd($siswa);
            $siswa->save();

            $timelineProgress = TimelineProgress::firstOrNew(['user_id' => $user->id]);
            $timelineProgress->current_step = 1;
            $timelineProgress->sma_id = $validated['sma_id'];
            // dd($timelineProgress);
            $timelineProgress->save();

            NotificationHistory::create([
                'user_id' => $user->id,
                'type' => 'success',
                'message' => 'Berhasil menyimpan SMA adan jalur pendaftaran.',
                'is_read' => false,
            ]);

            return redirect()->route('pendaftaran.sma.timeline', ['step' => 1])
                ->with('success', 'Jalur dan SMA berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan jalur dan SMA: ' . $e->getMessage());

            NotificationHistory::create([
                'user_id' => $user->id,
                'type' => 'error',
                'message' => 'Gagal menyimpan SMA dan jalur pendaftaran',
                'is_read' => false,
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }
    }


    public function showTimeline(Request $request, $pathStep = null)
    {        
        $progress = Auth::user()->timelineProgress->current_step ?? 1;
        $currentStep = $pathStep ?? $request->query('step', $progress);
        $currentStep = (int) $currentStep;
        if ($currentStep > $progress) {
            $currentStep = $progress;
        }
        
        $siswa = Auth::user()->siswa;

        // Logika Menghitung Nilai Akhir
        $allNilai = $siswa->semesters->pluck('nilai_semester');
        $totalNilai = $allNilai->sum();
        $jumlahEntri = $allNilai->count();
        $nilaiAkhir = $jumlahEntri > 0 ? $totalNilai / $jumlahEntri : 0;

        $siswa->refresh();
        /** @var \App\Models\User $user */
        $user = Auth::user()->load('siswa.ortu', 'semesters', 'raporFiles');
        $siswa = $user->siswa;
        $ortu  = $siswa->ortu ?? null;
        $sekolahAsals = SekolahAsal::all();
        $mapels = Mapel::all();
        $isPasswordChanged = Auth::user()->password_changed_at;
        $data_sma = DataSma::all();
        
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

        $selectedSma = DataSma::find($siswa->data_sma_id) ?? null;
        $jalurId = $siswa->jalur_pendaftaran_id ?? null;
        $smaLatitude = $selectedSma->latitude ?? -6.200000;
        $smaLongitude = $selectedSma->longitude ?? 106.916666;

        return view('registration.sma_form.timeline_sma', [
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
            'selectedSma' => $selectedSma,

            'nilaiAkhir' => $nilaiAkhir,
            'jalurId' => $jalurId,

            'smaLatitude' => $smaLatitude,
            'smaLongitude'=> $smaLongitude,

        ]);

    }
    
    public function savePendaftaran(Request $request): RedirectResponse
    {
        $currentStep = (int) $request->input('current_step');
        $siswa = Auth::user()->siswa;
        $jalurId = $siswa->jalur_pendaftaran_id;
        $message = '';

        try {
            DB::beginTransaction();

            switch ($currentStep) {
                case 1:
                    $this->_saveBiodata($request);
                    $message = 'Biodata Anda berhasil disimpan.';
                    break;
                case 2:
                    $this->_saveRapor($request);
                    $this->_calculateAndSaveNilaiAkhir();
                    $message = 'Nilai Anda berhasil disimpan.';
                    break;
                case 3:
                    if ($jalurId == 1) {
                        $this->_saveSertifikat($request);
                        $message = 'Data sertifikat berhasil disimpan.';
                    } elseif ($jalurId == 2) {
                        $this->_saveAfirmasi($request);
                        $message = 'Dokumen berhasil diupload';
                    } else {
                        $this->_saveZonasi($request);
                        $message = 'Lokasi Anda berhasil disimpan.';
                    }
                    break;
                case 4: 
                    $this->_saveSubmit($request);
                
                    // --- MODIFIKASI DIMULAI DI SINI ---
                    
                    // 1. Set status timeline progress (Opsional, pastikan current_step tidak bertambah lagi)
                    $timelineProgress = Auth::user()->timelineProgress;
                    
                    // 2. Tandai timeline sudah mencapai akhir
                    if (4 > $timelineProgress->current_step) { 
                        $timelineProgress->current_step = 4;
                        $timelineProgress->save();
                    }

                    NotificationHistory::create([
                        'user_id' => Auth::user()->id,
                        'type' => 'success',
                        'message' => 'Pendaftaran Anda berhasil disubmit dan selesai. Berkas Anda kini sedang diverifikasi oleh panitia.',
                        'is_read' => false,
                    ]);

                    DB::commit();

                    // 3. Lakukan REDIRECT ke halaman peringkat
                    return redirect()->route('siswa.peringkat')->with('success', 'Pendaftaran Anda berhasil diselesaikan! Silakan lihat posisi peringkat Anda.');
                    
                    // --- MODIFIKASI SELESAI DI SINI ---
                    
                    break;
                default:
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Langkah tidak valid.');
            }

            // Setelah data disimpan, tingkatkan langkah dan simpan progres
            $timelineProgress = Auth::user()->timelineProgress;
            $nextStep = (int)$currentStep + 1;
            
            if ($nextStep > $timelineProgress->current_step) {
                $timelineProgress->current_step = $nextStep;
                $timelineProgress->save();
            }

            NotificationHistory::create([
                'user_id' => Auth::user()->id,
                'type' => 'success',
                'message' => $message,
                'is_read' => false,
            ]);

            DB::commit();

            return redirect()->route('pendaftaran.sma.timeline', ['step' => $nextStep])
                ->with('success', $message);

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput()
                ->with('error', 'Gagal menyimpan data. Pastikan semua data sudah diisi dengan benar.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan data timeline: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    private function _saveBiodata(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            // file
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'akta_file' => 'nullable|file|mimes:pdf|max:2048',

            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255',

            // data siswa
            'jenis_kelamin' => 'required|string',
            'kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'desa' => 'required|string',
            'alamat' => 'required|string',
            'no_kk' => 'required|string',
            'nik' => 'required|string',
            'no_hp' => 'required|string',
            'nama_ayah' => 'required|string',
            'nama_ibu' => 'required|string',
            'email' => 'required|string',
            'agama' => 'required|string',
            'kebutuhan_k' => 'nullable|string',
            'sekolah_asal_id' => 'required|integer',
            
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

            $siswa = $user->siswa ?? new Siswa();
            $siswa->user_id = $user->id;

            if ($request->hasFile('foto')) {
                if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
                    Storage::disk('public')->delete($siswa->foto);
                }
                $siswa->foto = $request->file('foto')->store('profile_murid', 'public');
            }
            if ($request->hasFile('akta_file')) {
                if ($siswa->akta_file && Storage::disk('public')->exists($siswa->akta_file)) {
                    Storage::disk('public')->delete($siswa->akta_file);
                }
                $siswa->akta_file = $request->file('akta_file')->store('akta_murid', 'public');
            }

            $siswaData = collect($validatedData)->except([
                'nama_wali', 'tempat_lahir_wali', 'tanggal_lahir_wali', 'pekerjaan_wali', 'alamat_wali', 'foto', 'akta_file', 'email'
            ])->toArray();

            $siswa->fill($siswaData);
            $siswa->save();

            $ortuData = $request->only([
                'nama_wali', 'tempat_lahir_wali', 'tanggal_lahir_wali', 'pekerjaan_wali', 'alamat_wali'
            ]);

            Ortu::updateOrCreate(
                ['siswa_id' => $siswa->id],
                $ortuData
            );   
        });
    }    

    private function _saveRapor(Request $request)
    {
        $validated = $request->validate([
            'nilai' => 'required|array',
            'nilai.*.*' => 'nullable|numeric|min:0|max:100',
            'rapor_file' => 'nullable|array',
            'rapor_file.*' => 'nullable|mimes:pdf|max:1000'
        ]);

        $userId = Auth::id();
        $dataNilai = $validated['nilai'];
        $mapels = Mapel::all();
        $rapor_file = RaporFile::all();

        foreach ($dataNilai as $semester => $nilaiMapel) {
            if ($request->hasFile("rapor_file.{$semester}")) {
                $file = $request->file("rapor_file.{$semester}");

                $existingRaporFile = RaporFile::where('user_id', $userId)->where('semester', $semester)->first();

                if ($existingRaporFile && Storage::disk('public')->exists($existingRaporFile->file_rapor)) {
                    Storage::disk('public')->delete($existingRaporFile->file_rapor);
                }
                
                $fileName = $file->hashName('rapor_murid/' . $userId);
                $file->storeAs('rapor_murid/' . $userId, $fileName, 'public');

                RaporFile::updateOrCreate(
                    ['user_id' => $userId,
                    'semester' => $semester],
                    ['file_rapor' => $fileName]
                );
            }

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
    }

    private function _saveSertifikat(Request $request)
    {
    $validatedData = $request->validate([
        'sertifikat_file' => 'nullable|file|mimes:pdf|max:500',
    ]);

    $siswa = Auth::user()->siswa;

    if (!$siswa) {
        throw new \Exception('Data siswa tidak ditemukan.');
    }

    if ($request->hasFile('sertifikat_file')) {
        if ($siswa->sertifikat_file && Storage::disk('public')->exists($siswa->sertifikat_file)) {
            Storage::disk('public')->delete($siswa->sertifikat_file);
        }
        
        $filePath = $request->file('sertifikat_file')->store('sertifikat_murid', 'public');
        $siswa->sertifikat_file = $filePath;
        $siswa->save();
    }
    }

    private function _saveAfirmasi(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'document_afirmasi' => 'required|file|mimes:pdf|max:1024',
            'lat_siswa' => 'required|numeric',
            'lng_siswa' => 'required|numeric',
        ]);

        $siswa = Auth::user()->siswa;
        $selectedSma = DataSma::find($siswa->data_sma_id);

        if (!$siswa) {
            throw new \Exception('Data siswa atau SMA tidak ditemukan.');
        }

        if ($request->hasFile('document_afirmasi')) {
            if ($siswa->document_afirmasi && Storage::disk('public')->exists($siswa->document_afirmasi)) {
                Storage::disk('public')->delete($siswa->document_afirmasi);
            }

            $filePath = $request->file('document_afirmasi')->store('document_afirmasi_murid', 'public');
            $siswa->document_afirmasi = $filePath;
        }

        $siswa->latitude_siswa = $request->input('lat_siswa');
        $siswa->longitude_siswa = $request->input('lng_siswa');
        
        $distance = $this->calculateDistance(
            $selectedSma->latitude, $selectedSma->longitude,
            $siswa->latitude_siswa, $siswa->longitude_siswa
        );
        $siswa->jarak_ke_sma_km = round($distance, 2);

        $siswa->save();
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) 
    {
        // Konstanta Jari-jari Bumi dalam Kilometer
        $earthRadius = 6371; 

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c; // Jarak dalam KM

        return $distance;
    }

    private function _saveZonasi(Request $request)
    {
        $siswa = Auth::user()->siswa;
        $selectedSma = DataSma::find($siswa->data_sma_id);

        if (!$selectedSma) {
            throw new \Exception('SMA tujuan tidak ditemukan. Silakan ulangi dari Langkah 1.');
        }

        $request->validate([
            'lat_siswa' => 'required|numeric',
            'lng_siswa' => 'required|numeric',
        ]);
        
        $siswa->jalur_pendaftaran_id = 3; 
        $siswa->data_sma_id = $selectedSma->id; 
        $siswa->latitude_siswa = $request->input('lat_siswa');
        $siswa->longitude_siswa = $request->input('lng_siswa');
        
        $distance = $this->calculateDistance(
            $selectedSma->latitude, 
            $selectedSma->longitude,
            $siswa->latitude_siswa, 
            $siswa->longitude_siswa
        );
        $siswa->jarak_ke_sma_km = round($distance, 2);

        $siswa->save();
    }

    private function _saveSubmit(Request $request): RedirectResponse
    {
        $siswa = Auth::user()->siswa;
        $siswa->status_pendaftaran = 'completed';
        $siswa->save();
        $timelineProgress = Auth::user()->timelineProgress;
        if (4 > $timelineProgress->current_step) { 
            $timelineProgress->current_step = 4;
            $timelineProgress->save();
        }
        return redirect()->route('siswa.peringkat')
            ->with('success', 'Pendaftaran Anda berhasil diselesaikan! Silakan lihat posisi peringkat Anda.');
    }

    public function showPeringkatSiswa()
    {
        $spmbStatus = SpmbStatus::first();
        $selection_ended = $spmbStatus && $spmbStatus->status === 'closed';
        $siswa = Auth::user()->siswa;
        
        // Pastikan siswa sudah mendaftar (status completed) dan memilih SMA
        if (!$siswa || $siswa->status_pendaftaran != 'completed' || !$siswa->data_sma_id) {
            return redirect()->route('pendaftaran_sma')->with('gagal', 'Anda belum menyelesaikan pendaftaran.');
        }

        $sma_id = $siswa->data_sma_id;
        $jalur_id = $siswa->jalur_pendaftaran_id;
        $jalur_id_str = (string) $jalur_id;

        $kuotaJalur = 0;

        if ($sma_id) {
            // Asumsi Model DataSma tersedia
            $smaData = \App\Models\DataSma::find($sma_id);
            $totalKuotaSekolah = $smaData->kuota_siswa ?? 0;
            
            $persentase = self::KUOTA_PERSEN[$jalur_id_str] ?? 0; 
            $kuotaJalur = (int) floor($totalKuotaSekolah * $persentase);
        }
        
        // 1. Ambil query dasar untuk semua siswa di SMA dan jalur yang sama
        $query = Siswa::with('user', 'SekolahAsal')
            ->where('data_sma_id', $sma_id)
            ->where('jalur_pendaftaran_id', $jalur_id)
            ->where('status_pendaftaran', 'completed'); // Hanya hitung yang sudah selesai

        $mandatoryVerificationColumns = [
            'akta_file_verified',
            'rapor_files_verified',
            'surat_pernyataan_verified',
            'surat_keterangan_lulus_verified',
            'ijazah_file_verified'
        ];

        foreach ($mandatoryVerificationColumns as $column) {
            // SEMUA dokumen wajib harus 'terverifikasi' untuk masuk peringkat
            $query->where($column, 'terverifikasi');
        }

        // 2. Tentukan urutan (Sorting) sesuai Jalur Pendaftaran
        if ($jalur_id == 1) { // Prestasi
            $query->orderBy(DB::raw("CASE 
                WHEN verifikasi_sertifikat = 'terverifikasi' THEN 1 
                ELSE 0 
            END"), 'desc')->orderByDesc('nilai_akhir')->orderBy('tanggal_lahir', 'asc');
        } elseif ($jalur_id == 2) { // Afirmasi
            // Afirmasi harus terverifikasi untuk dihitung di peringkat akhir
            $query->where('verifikasi_afirmasi', 'terverifikasi')
                  ->orderBy('jarak_ke_sma_km', 'asc')
                  ->orderBy('tanggal_lahir', 'asc');
        } elseif ($jalur_id == 3) { // Zonasi
            $query->orderBy('jarak_ke_sma_km', 'asc')->orderBy('tanggal_lahir', 'asc');
        } else {
            // Jalur Umum/Lainnya (jika ada) diurutkan berdasarkan Nilai/Usia
            $query->orderByDesc('nilai_akhir')->orderBy('tanggal_lahir', 'asc');
        }

        if ($kuotaJalur > 0) {
            $query->limit($kuotaJalur);
        }

        // 3. Eksekusi query
        $allSiswas = $query->get();

        $documentLabels = [
            'akta_file_verified' => 'Akta Kelahiran',
            'rapor_files_verified' => 'Rapor Files',
            'surat_pernyataan_verified' => 'Surat Pernyataan',
            'surat_keterangan_lulus_verified' => 'Surat Keterangan Lulus',
            'ijazah_file_verified' => 'Ijazah',
            'verifikasi_afirmasi' => 'Verifikasi Afirmasi', // Hanya untuk Jalur ID 2
        ];

        $mandatoryColumns = [
            'akta_file_verified',
            'rapor_files_verified',
            'surat_pernyataan_verified',
            'surat_keterangan_lulus_verified',
            'ijazah_file_verified'
        ];

        // Tambahkan verifikasi afirmasi jika jalurnya ID 2 (Afirmasi)
        if ($siswa->jalur_pendaftaran_id == 2) {
            $mandatoryColumns[] = 'verifikasi_afirmasi';
        }

        $rejectedDocs = [];
        $statusVerifikasiSiswa = 'terverifikasi'; // Asumsi awal status terbaik

        foreach ($mandatoryColumns as $column) {
            // Ambil status, default 'pending' jika kolom kosong/null (misal, baru diunggah)
            $status = $siswa->{$column} ?? 'pending';
            
            // Prioritas status: Ditolak > Pending > Terverifikasi
            if ($status === 'ditolak') {
                // Jika ada satu saja ditolak, status akhir pasti ditolak
                $statusVerifikasiSiswa = 'ditolak';
                $rejectedDocs[] = $documentLabels[$column];
            } elseif ($status === 'pending' && $statusVerifikasiSiswa !== 'ditolak') {
                // Jika ada pending, dan belum ada yang ditolak, status akhir jadi pending
                $statusVerifikasiSiswa = 'pending';
                $rejectedDocs[] = $documentLabels[$column];
            }
        }

        $jalur_id_for_filter = $siswa->jalur_pendaftaran_id;

        $verifiedSiswas = $allSiswas->filter(function ($pendaftar) use ($jalur_id_for_filter) {
            // Kolom wajib default
            $filterMandatoryColumns = [
                'akta_file_verified', 'rapor_files_verified', 'surat_pernyataan_verified', 
                'surat_keterangan_lulus_verified', 'ijazah_file_verified'
            ];
            
            // Tambahkan verifikasi afirmasi jika jalurnya ID 2 (Afirmasi)
            if ($jalur_id_for_filter == 2) {
                $filterMandatoryColumns[] = 'verifikasi_afirmasi';
            }

            // Cek semua dokumen wajib harus 'terverifikasi'
            foreach ($filterMandatoryColumns as $column) {
                // Gunakan 'pending' sebagai default jika null, lalu cek apakah statusnya 'terverifikasi'
                if (($pendaftar->{$column} ?? 'pending') !== 'terverifikasi') {
                    return false; 
                }
            }
            
            return true; // Siswa ini lolos verifikasi wajib
        });

        $peringkat = $verifiedSiswas->search(function ($item) use ($siswa) {
            return $item->id === $siswa->id;
        });
        
        $peringkatSiswa = '';

        if ($statusVerifikasiSiswa === 'terverifikasi') {
            // Jika semua dokumen wajib sudah terverifikasi, baru hitung peringkat
            $peringkatSiswa = ($peringkat !== false) ? $peringkat + 1 : 'Diluar Kuota';
            
        } elseif ($statusVerifikasiSiswa === 'pending') {
            // Jika ada satu atau lebih dokumen yang masih pending verifikasi
            $peringkatSiswa = 'Menunggu Verifikasi';

        } elseif ($statusVerifikasiSiswa === 'ditolak') {
            // Jika ada satu atau lebih dokumen yang ditolak
            $peringkatSiswa = 'Verifikasi Ditolak'; // Atau pesan lain yang sesuai

        } else {
            // Kasus lain, mungkin status belum jelas
            $peringkatSiswa = 'Status Belum Ditentukan';
        }
        
        // Hilangkan duplikat dan buat string
        $rejectedDocumentsList = implode(', ', array_unique($rejectedDocs));

        $statusPenerimaan = $siswa->status_penerimaan ?? '';
        $userId = Auth::id() ?? 'guest';
        $hasNotified = session('result_notified_' . $userId);

        if ($selection_ended && in_array(strtolower($statusPenerimaan), ['diterima', 'ditolak']) && !$hasNotified) {
            $sekolahTujuan = $siswa->dataSma->nama_sma ?? 'Sekolah Tujuan';
            $jalur = $siswa->jalurPendaftaran->nama_jalur_pendaftaran ?? 'Pendaftaran';

            if (strtolower($statusPenerimaan) === 'diterima') {
                $message = "🎉 Selamat! Anda dinyatakan DITERIMA di {$sekolahTujuan} melalui Jalur {$jalur}.";
                session()->flash('success', $message);
            } else {
                $message = "😔 Mohon Maaf. Anda dinyatakan DITOLAK di {$sekolahTujuan}. Hasil seleksi telah dirilis.";
                session()->flash('error', $message);
            }
            session(['result_notified_' . $userId => true]);
        }

        // 5. Kirim data ke view
        return view('registration.peringkat_murid', [
            'siswa' => $siswa,
            'allSiswas' => $allSiswas,
            'peringkatSiswa' => $peringkatSiswa,
            'totalPendaftar' => $allSiswas->count(),
            'kuotaJalur' => $kuotaJalur,
            'statusVerifikasiSiswa' => $statusVerifikasiSiswa,
            'rejectedDocumentsList' => $rejectedDocumentsList,
            'selection_ended' => $selection_ended,
        ]);
    }

    public function tarikBerkas(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa || $siswa->status_pendaftaran !== 'completed') {
            return redirect()->route('siswa.peringkat')->with('error', 'Pendaftaran belum selesai atau data tidak ditemukan.');
        }

        try {
            DB::beginTransaction();

            // 1. Hapus file fisik lama jika ada (Sertifikat dan Afirmasi)
            if ($siswa->sertifikat_file && Storage::disk('public')->exists($siswa->sertifikat_file)) {
                Storage::disk('public')->delete($siswa->sertifikat_file);
            }
            if ($siswa->document_afirmasi && Storage::disk('public')->exists($siswa->document_afirmasi)) {
                Storage::disk('public')->delete($siswa->document_afirmasi);
            }

            // 2. Reset kolom data pendaftaran Siswa
            $siswa->data_sma_id = null;
            $siswa->jalur_pendaftaran_id = null;
            $siswa->sertifikat_file = null;
            $siswa->verifikasi_sertifikat = 'pending';
            $siswa->document_afirmasi = null;
            $siswa->verifikasi_afirmasi = 'pending';
            $siswa->status_pendaftaran = 'pending'; // Kembali ke status belum selesai

            $siswa->akta_file_verified = 'pending';
            $siswa->rapor_files_verified = 'pending';
            $siswa->surat_pernyataan_verified = 'pending';
            $siswa->surat_keterangan_lulus_verified = 'pending';
            $siswa->ijazah_file_verified = 'pending';
            
            $siswa->save();

            // 3. Reset Progress Timeline ke langkah 0 atau 1 (kembali ke awal)
            $timelineProgress = $user->timelineProgress ?? new TimelineProgress(['user_id' => $user->id]);
            $timelineProgress->current_step = 0; // Mulai dari pemilihan SMA/Jalur
            $timelineProgress->sma_id = null;
            $timelineProgress->save();

            DB::commit();

            NotificationHistory::create([
                // User ID yang menerima notifikasi (ID siswa yang sedang login)
                'user_id' => Auth::id(), 
                'type' => 'success', // Menggunakan 'info' karena ini adalah aksi yang diminta user
                'message' => 'Berkas pendaftaran Anda berhasil ditarik. Semua data pendaftaran SMA/Jalur telah direset. Anda dapat mendaftar ulang sekarang.',
                'is_read' => false,
                // created_by_user_id tidak perlu diisi karena aksi dilakukan oleh user sendiri
            ]);

            return redirect()->route('pendaftaran_sma')->with('success', 'Berkas pendaftaran Anda berhasil ditarik. Silakan lakukan pendaftaran ulang.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menarik berkas: ' . $e->getMessage());

            NotificationHistory::create([
                'user_id' => Auth::id(), 
                'type' => 'error',
                'message' => 'Gagal menarik berkas pendaftaran Anda. Terjadi kesalahan sistem. Silakan coba lagi.',
                'is_read' => false,
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menarik berkas.');
        }
    }
}
