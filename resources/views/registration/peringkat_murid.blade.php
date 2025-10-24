<x-app-layout>
    <div class="py-5">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-none sm:shadow-xl sm:rounded-lg p-2">
                @if ($selection_ended)
                    <div class="mb-5 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded-lg" role="alert">
                        <p class="font-bold">Pendaftaran Telah Berakhir</p>
                        <p>Hasil peringkat yang Anda lihat di bawah adalah <span class="font-bold text-yellow-800 underline">Hasil Akhir</span> SPMB 2026/2027.</p>
                    </div>
                @else
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center w-full bg-gray-50 pb-3 border-b">Peringkat Sementara</h2>
                @endif

                @if (session('berhasil'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        {{ session('berhasil') }}
                    </div>
                @endif
                
                <div class="grid grid-cols-3 md:grid-cols-3 gap-2 mb-8">
                    <div class="bg-blue-100 px-4 py-4 rounded-lg shadow-sm text-center flex flex-col items-center justify-center">
                        <p class="text-xs mb-[5px] font-medium text-blue-600">SMA Tujuan</p>
                        <p class="text-sm/[14px] font-bold text-gray-800">{{ $siswa->dataSma->nama_sma ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-blue-100 px-4 rounded-lg shadow-sm text-center flex flex-col items-center justify-center">
                        <p class="text-xs mb-[5px] font-medium text-blue-600">Jalur</p>
                        <p class="text-sm/[14px] font-bold text-gray-800">{{ $siswa->jalurPendaftaran->nama_jalur_pendaftaran ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-green-100 px-4 rounded-lg shadow-sm text-center flex flex-col items-center justify-center">
                        <p class="text-xs mb-[5px] font-medium text-green-600">Peringkat</p>
                        <p class="text-sm/[14px] font-extrabold text-green-800">{{ $peringkatSiswa }} / {{ $totalPendaftar }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" style="table-layout: fixed;">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="py-3 ps-5 pe-10 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                @if ($siswa->jalur_pendaftaran_id == 3 || $siswa->jalur_pendaftaran_id == 2)
                                    <th class="py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jarak</th>
                                @else
                                    <th class="py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">NA</th>
                                @endif
                            </tr>
                        </thead>
                    </table>
                    <div class="max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200" style="table-layout: fixed;">
                            <tbody class="bg-white divide-y divide-gray-200">
                                
                                {{-- Inisialisasi daftar kolom wajib verifikasi --}}
                                <?php
                                    $requiredVerifications = [
                                        'akta_file_verified',
                                        'rapor_files_verified',
                                        'surat_pernyataan_verified',
                                        'surat_keterangan_lulus_verified',
                                        'ijazah_file_verified',
                                    ];
                                ?>
                                
                                {{-- Reset peringkat yang akan ditampilkan --}}
                                @php $displayedRank = 0; @endphp

                                @foreach ($allSiswas as $index => $pendaftar)
                                    <?php
                                        $isVerified = true;
                                        
                                        // 1. Cek semua dokumen wajib
                                        foreach ($requiredVerifications as $column) {
                                            // 🔥 PASTIKAN PENGECEKAN INI DILAKUKAN:
                                            // Jika kolom bernilai NULL, ia akan dianggap 'pending' dan gagal diverifikasi.
                                            if (($pendaftar->{$column} ?? 'pending') !== 'terverifikasi') { 
                                                $isVerified = false;
                                                break;
                                            }
                                        }

                                        // 2. Cek dokumen Afirmasi (wajib hanya untuk jalur ID 2)
                                        if ($isVerified && $pendaftar->jalur_pendaftaran_id == 2) {
                                            if (($pendaftar->verifikasi_afirmasi ?? 'pending') !== 'terverifikasi') {
                                                $isVerified = false;
                                            }
                                        }

                                        // Jika berkas belum diverifikasi, JANGAN TAMPILKAN
                                        if (!$isVerified) {
                                            continue;
                                        }

                                        // Jika berkas diverifikasi, naikkan peringkat yang ditampilkan
                                        $displayedRank++;
                                    ?>
                                    
                                    <tr @if ($pendaftar->id === $siswa->id) class="bg-yellow-50 font-bold" @endif>
                                        {{-- Tampilkan peringkat yang sudah difilter --}}
                                        <td class="px-3 py-4 text-center text-sm text-gray-900">{{ $displayedRank }}</td> 
                                        <td class="ps-5 py-4 whitespace-nowrap text-sm text-gray-900 flex items-center gap-x-1">
                                            @if ($pendaftar->id === $siswa->id) 
                                                {{-- Jika data diri sendiri: Hanya tampilkan label (Anda), Tarik Berkas Dihapus --}}
                                                <span class="text-sm text-gray-900">{{ $pendaftar->user->name }}</span>
                                            @else
                                                {{-- Jika data siswa lain: Tampilkan tombol Lihat Data --}}
                                                <button 
                                                    onclick="showSiswaDetail({{ 
                                                        json_encode([
                                                            'nama' => $pendaftar->user->name,
                                                            'sekolah_asal' => $pendaftar->sekolahAsal->nama_sekolah ?? 'N/A',
                                                            'tanggal_lahir' => \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->format('d F Y'),
                                                            'usia' => floor(\Carbon\Carbon::parse($pendaftar->tanggal_lahir)->diffInYears(\Carbon\Carbon::now())),
                                                            'nilai_akhir' => $pendaftar->nilai_akhir,
                                                            'jenis_kelamin' => $pendaftar->jenis_kelamin,
                                                        ]) 
                                                    }})" >
                                                    {{ $pendaftar->user->name }} 
                                                </button>
                                            @endif
                                            {{-- Tampilkan icon sertifikat jika terverifikasi (opsional) --}}
                                            @if ($pendaftar->verifikasi_sertifikat === 'terverifikasi')
                                                <div class="bg-green-500 rounded-md" title="Verifikasi Sertifikat (Prestasi) Terverifikasi">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="white" class="size-4">
                                                        <path d="M6 6v4h4V6H6Z" />
                                                        <path fill-rule="evenodd" d="M5.75 1a.75.75 0 0 0-.75.75V3a2 2 0 0 0-2 2H1.75a.75.75 0 0 0 0 1.5H3v.75H1.75a.75.75 0 0 0 0 1.5H3v.75H1.75a.75.75 0 0 0 0 1.5H3a2 2 0 0 0 2 2v1.25a.75.75 0 0 0 1.5 0V13h.75v1.25a.75.75 0 0 0 1.5 0V13h.75v1.25a.75.75 0 0 0 1.5 0V13a2 2 0 0 0 2-2h1.25a.75.75 0 0 0 0-1.5H13v-.75h1.25a.75.75 0 0 0 0-1.5H13V6.5h1.25a.75.75 0 0 0 0-1.5H13a2 2 0 0 0-2-2V1.75a.75.75 0 0 0-1.5 0V3h-.75V1.75a.75.75 0 0 0-1.5 0V3H6.5V1.75A.75.75 0 0 0 5.75 1ZM11 4.5a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V5a.5.5 0 0 1 .5-.5h6Z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            @endif
                                            {{-- @if ($pendaftar->id === $siswa->id) (Anda) @endif --}}
                                        </td>
                                        
                                        {{-- Data Peringkat (Jarak / Nilai Rata-rata) --}}
                                        @if ($siswa->jalur_pendaftaran_id == 3 || $siswa->jalur_pendaftaran_id == 2)
                                            <td class="py-4 text-center text-sm text-gray-900">{{ $pendaftar->jarak_ke_sma_km }} KM</td>
                                        @else
                                            <td class="py-4 text-center text-sm text-gray-900">{{ $pendaftar->nilai_akhir }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @php
                    $totalSiswaTerverifikasi = $allSiswas->filter(function ($siswa) {
                        return $siswa->verifikasi_sertifikat === 'terverifikasi';
                    })->count();
                @endphp

                <div class="mt-8 text-sm text-gray-500">
                    <p> Ketetarangan : <br>
                        - Peringkat dihitung berdasarkan kriteria seleksi jalur {{ $siswa->jalurPendaftaran->nama_jalur_pendaftaran ?? '' }} (Nilai Akhir dan Usia untuk Prestasi, Jarak dan Usia untuk Zonasi/Afirmasi).
                    </p>                    
                    <div class="flex items-center gap-x-1">
                    @if ($totalSiswaTerverifikasi > 0)
                    -   <div class="bg-green-500 rounded-md w-fit">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="white" class="size-4">
                                <path d="M6 6v4h4V6H6Z" />
                                <path fill-rule="evenodd" d="M5.75 1a.75.75 0 0 0-.75.75V3a2 2 0 0 0-2 2H1.75a.75.75 0 0 0 0 1.5H3v.75H1.75a.75.75 0 0 0 0 1.5H3v.75H1.75a.75.75 0 0 0 0 1.5H3a2 2 0 0 0 2 2v1.25a.75.75 0 0 0 1.5 0V13h.75v1.25a.75.75 0 0 0 1.5 0V13h.75v1.25a.75.75 0 0 0 1.5 0V13a2 2 0 0 0 2-2h1.25a.75.75 0 0 0 0-1.5H13v-.75h1.25a.75.75 0 0 0 0-1.5H13V6.5h1.25a.75.75 0 0 0 0-1.5H13a2 2 0 0 0-2-2V1.75a.75.75 0 0 0-1.5 0V3h-.75V1.75a.75.75 0 0 0-1.5 0V3H6.5V1.75A.75.75 0 0 0 5.75 1ZM11 4.5a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V5a.5.5 0 0 1 .5-.5h6Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="font-bold">
                            <span class="text-green-600">Siswa Dengan Sertifikat :</span> {{ $totalSiswaTerverifikasi }} siswa
                        </p>
                    @endif
                    </div>
                </div>

                <div class="overflow-x-auto mt-4">
                    {{-- 🛑 KONDISI 1: DITOLAK (DOKUMEN BERMASALAH) 🛑 --}}
                    @if ($statusVerifikasiSiswa === 'ditolak')
                        <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg mb-6">
                            <p class="font-bold">❌ Pendaftaran DITOLAK karena Dokumen Bermasalah.</p>
                            <p class="text-sm">Mohon maaf, beberapa dokumen wajib Anda telah Ditolak oleh panitia.</p>
                            {{-- Variabel $rejectedDocumentsList harus berisi nama dokumen yang ditolak, contoh: "Akta Kelahiran, Verifikasi Afirmasi" --}}
                            <p class="mt-2 text-sm">Dokumen yang bermasalah: 
                                <span class="font-bold underline">{{ $rejectedDocumentsList ?? 'Dokumen Wajib (Akta, Rapor, SKL, Ijazah)' }}</span>.
                            </p>
                            <p class="mt-2 text-sm">Silakan Tarik Berkas Anda dan lakukan pendaftaran ulang setelah memperbaiki dokumen tersebut.</p>
                        </div>

                    {{-- 🛑 KONDISI 2: PENDING (DOKUMEN BELUM DIVERIFIKASI) 🛑 --}}
                    @elseif ($statusVerifikasiSiswa === 'pending')
                        <div class="p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded-lg mb-6">
                            <p class="font-bold">⏳ Menunggu Verifikasi Dokumen.</p>
                            <p class="text-sm">Status verifikasi dokumen wajib Anda saat ini adalah PENDING. Silakan tunggu proses verifikasi selesai sebelum menentukan kelolosan.</p>
                        </div>
                    
                    {{-- 🟢 KONDISI 3: LOLOS KUOTA DAN DOKUMEN TERVERIFIKASI (Zona Hijau) 🟢 --}}
                    @elseif ($peringkatSiswa !== 'Diluar Kuota' && $statusVerifikasiSiswa === 'terverifikasi' && !$selection_ended)
                        <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg mb-6">
                            <p class="font-bold">✅ Anda Berada Di Zona Hijau.</p>
                            <p class="text-sm">Sekarang anda berada didalam kuota pendaftaran dari total {{ $kuotaJalur }} kuota.</p>
                        </div>

                    @elseif ($selection_ended && $siswa->status_penerimaan == 'diterima')
                        <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg mb-6">
                            <p class="font-bold text-lg">🎉Selamat Anda Diterima Di Sekolah Pilihan Anda.🎉</p>
                        </div>
                    
                    @elseif ($selection_ended && $siswa->status_penerimaan == 'ditolak')
                        <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg mb-6">
                            <p class="font-bold text-lg">❌Mohon Maaf, Sepertinya Anda Tidak Memenuhi Syarat Kelulusan.❌</p>
                        </div>
                        
                    {{-- 🔴 KONDISI 4: TIDAK LOLOS KUOTA (Tapi dokumen sudah terverifikasi) 🔴 --}}
                    @else
                        <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg mb-6">
                            <p class="font-bold">❌ Mohon Maaf.</p>
                            <p class="text-sm">Status dokumen Anda sudah terverifikasi, namun Anda berada di luar kuota yang tersedia ({{ $kuotaJalur }} siswa).</p>
                            <p class="text-sm mt-2">Anda dapat mempertimbangkan untuk menarik berkas dan mendaftar di jalur atau SMA lain.</p>
                        </div>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    {{-- SISA KODE TABEL LANJUTAN ANDA DI BAWAH INI (TIDAK BERUBAH) --}}
                    <table class="min-w-full divide-y divide-gray-300 shadow-md rounded-lg">
                        <thead class="bg-blue-600">
                            <tr>
                                <th class="px-2 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">No</th>
                                @if ($siswa->jalur_pendaftaran_id == 1 || $siswa->jalur_pendaftaran_id == 2)
                                    <th class="px-2  py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Status Dokumen</th>
                                @endif
                                <th class="px-2 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                                    @if (!$selection_ended)       
                                        Aksi
                                    @else
                                        Hasil Seleksi                             
                                    @endif
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">

                            <tr class="bg-blue-50">
                                <td class="px-2 py-4 text-center whitespace-nowrap text-sm font-extrabold text-blue-700">
                                    {{ $peringkatSiswa }}
                                </td>                         
                                {{-- Kolom Status Dokumen --}}
                                @if ($siswa->jalur_pendaftaran_id == 1 || $siswa->jalur_pendaftaran_id == 2)
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <p class="w-fit rounded-full px-2 font-extrabold mx-auto
                                            @switch($statusVerifikasiSiswa)
                                                @case('terverifikasi') bg-green-100 text-green-800 @break
                                                @case('pending') bg-yellow-100 text-yellow-800 @break
                                                @case('ditolak') bg-red-100 text-red-800 @break
                                            @endswitch">{{ $statusVerifikasiSiswa }} 
                                        </p>
                                    </td>
                                @endif

                                @if (!$selection_ended)
                                <td class="px-2 py-4 text-center whitespace-nowrap text-sm text-gray-900">
                                    <form action="{{ route('siswa.tarik_berkas') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menarik semua berkas pendaftaran? Aksi ini akan menghapus pilihan SMA, jalur, dan semua berkas yang telah diunggah. Anda harus mendaftar dari awal.');">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">
                                            Tarik Berkas
                                        </button>
                                    </form>
                                </td>
                                @else
                                <td class="px-2 py-4 text-center whitespace-nowrap text-sm font-extrabold">
                                    @if ($siswa->status_penerimaan == 'diterima')
                                        <p class="text-green-800 underline">Diterima</p>
                                    @else
                                        <p class="text-red-500 underline">Tidak Diterima</p>
                                    @endif
                                </td> 
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="siswaDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Detail Calon Siswa</h3>
                        <div id="siswaDetailContent">
                            {{-- Content will be populated by JavaScript --}}
                        </div>
                        <div class="mt-6 text-right">
                            <button onclick="document.getElementById('siswaDetailModal').classList.add('hidden')" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                    function showSiswaDetail(data) {
                        const content = document.getElementById('siswaDetailContent');
                        const modal = document.getElementById('siswaDetailModal');
                        
                        let htmlContent = `
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between border-b border-gray-100 pb-1">
                                    <span class="font-medium">Nama</span>
                                    <span>${data.nama}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1">
                                    <span class="font-medium">Sekolah Asal</span>
                                    <span>${data.sekolah_asal}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1">
                                    <span class="font-medium">Jenis Kelamin</span>
                                    <span>${data.jenis_kelamin}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1">
                                    <span class="font-medium">Tgl Lahir</span>
                                    <span>${data.tanggal_lahir}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1">
                                    <span class="font-medium">Usia</span>
                                    <span>${data.usia} tahun</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1">
                                    <span class="font-medium">Nilai Akhir Rata-rata</span>
                                    <span class="font-bold text-blue-700">${data.nilai_akhir}</span>
                                </div>
                            </div>
                        `;

                        content.innerHTML = htmlContent;
                        modal.classList.remove('hidden');
                    }
                </script>
            </div>
        </div>
    </div>
</x-app-layout>