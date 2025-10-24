@extends('admin_sekolah.layouts.admin_layout')

@section('content')
    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('admin.jalur_pendaftaran.show', $siswa->jalur_pendaftaran_id) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">&larr; Kembali ke Daftar Siswa</a>
            
            {{-- @include('components.notification') Pastikan Anda memiliki component notifikasi ini --}}

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- KOTAK 1: BIODATA DAN ORANG TUA --}}
                <div class="md:col-span-2 bg-white shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4 border-b pb-2">1. Data Siswa & Orang Tua</h3>
                    
                    {{-- FOTO SISWA --}}
                    @if ($siswa->foto)
                        <img src="{{ Storage::url($siswa->foto) }}" alt="Foto Siswa" class="w-32 h-32 object-cover rounded-full mb-4 ring-2 ring-blue-500">
                    @else
                        <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center mb-4">No Photo</div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="font-semibold">Nama:</span> {{ $siswa->user->name }}</div>
                        <div><span class="font-semibold">Email:</span> {{ $siswa->user->email }}</div>
                        <div><span class="font-semibold">NISN:</span> {{ $siswa->nisn }}</div>
                        <div><span class="font-semibold">Jalur:</span> {{ $siswa->jalurPendaftaran->nama_jalur_pendaftaran ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Sekolah Asal:</span> {{ $siswa->sekolahAsal->nama_sekolah ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Tgl Lahir:</span> {{ $siswa->tanggal_lahir }}</div>
                        <div><span class="font-semibold">Jenis Kelamin:</span> {{ $siswa->jenis_kelamin }}</div>
                        <div><span class="font-semibold">Agama:</span> {{ $siswa->agama }}</div>
                        
                        <div class="col-span-2 mt-4 border-t pt-4">
                            <span class="font-bold">Alamat:</span> {{ $siswa->alamat ?? 'N/A' }} (Jarak: {{ $siswa->jarak_ke_sma_km ?? 'N/A' }} KM)
                        </div>
                        
                        {{-- Ganti blok Data Wali (Guardian) di dalam KOTAK 1 --}}
                        <div class="col-span-2 mt-4 border-t pt-4">
                            {{-- <h4 class="font-bold mb-3">Data Wali/Orang Tua</h4> --}}
                            
                            @php
                                // Ambil objek Ortu tunggal
                                $wali = $siswa->ortu; 
                            @endphp
                            
                            {{-- 🔥 Cek apakah objek Ortu tunggal ada (tidak null) 🔥 --}}
                            @if ($wali)
                                
                                <div class="border-b mb-3 pb-3">
                                    
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div><span class="font-semibold">Nama Wali:</span> {{ $wali->nama_wali ?? 'N/A' }}</div>
                                        <div><span class="font-semibold">Pekerjaan Wali:</span> {{ $wali->pekerjaan_wali ?? 'N/A' }}</div>
                                        <div><span class="font-semibold">Tempat Lahir Wali:</span> {{ $wali->tempat_lahir_wali ?? 'N/A' }}</div>
                                        
                                        {{-- Tampilkan Tanggal Lahir Mentah (untuk menghindari error Carbon yang menyebabkan layar putih) --}}
                                        <div>
                                            <span class="font-semibold">Tanggal Lahir Wali:</span> 
                                            {{ $wali->tanggal_lahir_wali ?? 'N/A' }}
                                        </div>

                                        <div class="col-span-2"><span class="font-semibold">Alamat Wali:</span> {{ $wali->alamat_wali ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                
                            @else
                                <div>Data Wali/Orang Tua tidak lengkap atau belum diisi.</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- KOTAK 2: STATUS DOKUMEN DAN VERIFIKASI --}}
                <div class="bg-white shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4 border-b pb-2">2. Verifikasi Dokumen</h3>

                    @php
                        // Daftar dokumen yang akan diverifikasi
                        $dokumenList = [
                            'akta' => ['label' => 'Akta Kelahiran', 'file_col' => 'akta_file', 'verified_col' => 'akta_file_verified', 'jalur_khusus' => 0],
                            'rapor' => ['label' => 'Rapor Files', 'file_col' => 'rapor_files', 'verified_col' => 'rapor_files_verified', 'jalur_khusus' => 0], // file_col ini hanya dummy untuk rapor
                            'surat_pernyataan' => ['label' => 'Surat Pernyataan', 'file_col' => 'surat_pernyataan', 'verified_col' => 'surat_pernyataan_verified', 'jalur_khusus' => 0],
                            'surat_keterangan_lulus' => ['label' => 'Surat Keterangan Lulus', 'file_col' => 'surat_keterangan_lulus', 'verified_col' => 'surat_keterangan_lulus_verified', 'jalur_khusus' => 0],
                            'ijazah' => ['label' => 'Ijazah', 'file_col' => 'ijazah_file', 'verified_col' => 'ijazah_file_verified', 'jalur_khusus' => 0],
                            
                            'sertifikat' => ['label' => 'Sertifikat (Prestasi)', 'file_col' => 'sertifikat_file', 'verified_col' => 'verifikasi_sertifikat', 'jalur_khusus' => 1], // Jalur 1
                            'document_afirmasi' => ['label' => 'Dok. Afirmasi (KIP/PKH)', 'file_col' => 'document_afirmasi', 'verified_col' => 'verifikasi_afirmasi', 'jalur_khusus' => 2], // Jalur 2
                        ];

                        // Mendefinisikan rute verifikasi khusus
                        $specialRoutes = [
                            'sertifikat' => 'admin.verifikasi_sertifikat',
                            'document_afirmasi' => 'admin.verifikasi_afirmasi',
                        ];
                    @endphp

                    <div class="space-y-4">
                        @foreach ($dokumenList as $key => $dokumen)
                            @php
                                $isRapor = ($key === 'rapor');
                                // Cek file kolom biasa (misal akta_file)
                                $filePath = $siswa->{$dokumen['file_col']}; 
                                $status = $siswa->{$dokumen['verified_col']} ?? 'pending';
                                
                                // KONDISI TAMPILAN UTAMA
                                $showDoc = ($dokumen['jalur_khusus'] === 0) || ($siswa->jalur_pendaftaran_id == $dokumen['jalur_khusus']);

                                // Tentukan apakah file sudah diunggah:
                                $isUploaded = (bool)$filePath;

                                // PERBAIKAN KRITIS UNTUK RAPOR FILES: Cek keberadaan file di relasi 'raporFiles'
                                if ($isRapor) {
                                    // Cek apakah ada setidaknya satu entri di mana kolom 'file_rapor' tidak kosong
                                    $isUploaded = $siswa->raporFiles->contains(fn($r) => !empty($r->file_rapor));
                                }
                            @endphp
                            
                            @if ($showDoc)
                                
                                <div class="p-3 border rounded-lg">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="text-sm font-semibold">{{ $dokumen['label'] }}</div>
                                        @php
                                            $color = match($status) {
                                                'terverifikasi' => 'bg-green-100 text-green-800',
                                                'ditolak' => 'bg-red-100 text-red-800',
                                                default => 'bg-yellow-100 text-yellow-800',
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full {{ $color }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </div>

                                    {{-- Link Lihat File --}}
                                    @if ($filePath)
                                        <a href="{{ Storage::url($filePath) }}" target="_blank" class="text-blue-600 hover:underline text-xs block mb-2">
                                            Lihat File <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    {{-- Tampilan Rapor Files --}}
                                    @elseif ($isRapor && $isUploaded)
                                        <div class="mt-1 space-y-1 mb-2">
                                            <span class="text-gray-700 text-xs block font-semibold mb-1">File Rapor Terunggah:</span>
                                            
                                            {{-- Hanya tampilkan link untuk file rapor yang kolom file_rapor-nya terisi --}}
                                            @foreach ($siswa->raporFiles as $raporFile)
                                                @if (!empty($raporFile->file_rapor)) 
                                                    <a href="{{ asset('storage/' . $raporFile->file_rapor) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:underline text-xs bg-blue-50 py-0.5 px-2 rounded-full mr-2">
                                                        Semester {{ $raporFile->semester ?? $raporFile->semester_id }} <i class="fas fa-external-link-alt ml-1"></i>
                                                    </a>
                                                @endif
                                            @endforeach
                                            
                                        </div>
                                        <span class="text-xs text-gray-500 block">
                                            Status verifikasi di atas berlaku untuk keseluruhan data rapor yang diinput.
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs block mb-2">File belum diunggah.</span>
                                    @endif

                                    {{-- Tombol Verifikasi --}}
                                    @if ($isUploaded)
                                        @php
                                            $verificationRouteName = $specialRoutes[$key] ?? 'admin.siswa.verifikasi_dokumen';
                                            $routeParams = isset($specialRoutes[$key]) ? [$siswa] : [$siswa, $key];
                                        @endphp

                                        <div class="flex space-x-2 mt-2">
                                            <form action="{{ route($verificationRouteName, $routeParams) }}" method="POST" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="status" value="terverifikasi">
                                                <button type="submit" class="text-xs py-1 px-2 rounded-md transition ease-in-out duration-150 {{ $status == 'terverifikasi' ? 'bg-green-300 text-gray-700 opacity-75 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 text-white' }}"
                                                    @if ($status == 'terverifikasi') disabled @endif>
                                                    Terima
                                                </button>
                                            </form>

                                            <form action="{{ route($verificationRouteName, $routeParams) }}" method="POST" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="status" value="ditolak">
                                                <button type="submit" class="text-xs py-1 px-2 rounded-md transition ease-in-out duration-150 {{ $status == 'ditolak' ? 'bg-red-300 text-gray-700 opacity-75 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 text-white' }}"
                                                    @if ($status == 'ditolak') disabled @endif>
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">Aksi tidak tersedia (File belum diunggah).</span>
                                    @endif

                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Bagian Tampilan Rapor Detail (Opsional) --}}
            @if ($siswa->raporFiles->isNotEmpty())
                <div class="bg-white shadow-xl sm:rounded-lg p-6 mt-6">
                    <h3 class="text-xl font-bold mb-4 border-b pb-2">3. Detail Nilai Rapor</h3>
                    <div class="overflow-x-auto">
                        {{-- Logika untuk menampilkan tabel nilai rapor di sini --}}
                        <p class="text-sm text-gray-500">Nilai akhir rata-rata: <span class="font-bold text-blue-700">{{ $siswa->nilai_akhir }}</span></p>
                        {{-- Anda bisa menampilkan semua Mapel dan nilai per semester di sini --}}
                        {{-- Contoh: @include('admin.partials.rapor_detail_table', ['raporFiles' => $siswa->raporFiles]) --}}
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection