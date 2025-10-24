<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
    <div class="border border-gray-400 p-6 rounded-lg">
        <p class="font-semibold text-lg pb-4 mb-4 border-b border-gray-800 w-full text-center sm:text-left">
            Resume Pendaftaran SMA Jalur Prestasi
        </p>

        {{-- DATA CALON MURID --}}
        <p class="font-semibold text-base mb-2">Data Calon Murid</p>
        <div class="overflow-x-auto">
            <div class="grid grid-cols-1 sm:grid-cols-6 border border-gray-300 mb-4">
                <ul class="col-span-2 border-b sm:border-b-0 sm:border-e border-gray-300 bg-gray-50">
                    <li class="ps-3 py-2 border-b border-gray-300">Nama Lengkap</li>
                    <li class="ps-3 py-2 border-b border-gray-300">NISN</li>
                    <li class="ps-3 py-2 border-b border-gray-300">Alamat</li>
                    <li class="ps-3 py-2 border-b border-gray-300">Tanggal Lahir</li>
                    <li class="ps-3 py-2 border-b border-gray-300">Sekolah Asal</li>
                    <li class="ps-3 py-2">No HP</li>
                </ul>
                <ul class="col-span-4">
                    <li class="ps-3 py-2 border-b border-gray-300 break-words">{{ Auth::user()->name }}</li>
                    <li class="ps-3 py-2 border-b border-gray-300 break-words">{{ $siswa->nisn }}</li>
                    <li class="ps-3 py-2 border-b border-gray-300 break-words">{{ $siswa->alamat }}</li>
                    <li class="ps-3 py-2 border-b border-gray-300">{{ $siswa->tanggal_lahir }}</li>
                    <li class="ps-3 py-2 border-b border-gray-300">{{ $siswa->sekolahAsal->nama_sekolah ?? '-' }}</li>
                    <li class="ps-3 py-2">{{ $siswa->no_hp }}</li>
                </ul>
            </div>
        </div>

        {{-- DATA PENDAFTARAN --}}
        <p class="font-semibold text-base mb-2">Data Pendaftaran</p>
        <div class="overflow-x-auto">
            <div class="grid grid-cols-1 sm:grid-cols-6 border border-gray-300 mb-4">
                <ul class="col-span-2 border-b sm:border-b-0 sm:border-e border-gray-300 bg-gray-50">
                    <li class="ps-3 py-2 border-b border-gray-300">Jalur Pendaftaran</li>
                    <li class="ps-3 py-2">Sekolah Tujuan</li>
                </ul>
                <ul class="col-span-4">
                    <li class="ps-3 py-2 border-b border-gray-300">{{ $siswa->jalurPendaftaran->nama_jalur_pendaftaran ?? '-' }}</li>
                    <li class="ps-3 py-2">{{ $siswa->dataSma->nama_sma ?? '-' }}</li>
                </ul>
            </div>
        </div>

        {{-- NILAI RAPOR --}}
        <input type="hidden" name="nilai_akhir" value="{{ number_format($nilaiAkhir, 2) }}">
        <p class="font-semibold text-base mb-2 mt-4">Data Nilai Rapor</p>
        <div class="overflow-x-auto">
            <div class="min-w-[600px] border border-gray-300 text-sm sm:text-base">
                <div class="grid grid-cols-7">
                    <p class="col-span-2 border-e border-b border-gray-300 py-2 text-center font-semibold bg-gray-50">Mata Pelajaran</p>
                    <p class="col-span-5 border-b border-gray-300 py-2 text-center font-semibold bg-gray-50">Nilai Rapor</p>

                    <p class="col-span-2"></p>
                    @for ($i = 1; $i <= 5; $i++)
                        <p class="col-span-1 border-e border-b border-gray-300 py-2 text-center font-medium bg-gray-100">
                            Semester {{ $i }}
                        </p>
                    @endfor
                </div>

                @php
                    $uniqueMapels = $siswa->semesters->pluck('mapels')->unique('id');
                @endphp

                @foreach ($uniqueMapels as $mapel)
                    <div class="grid grid-cols-7">
                        <p class="col-span-2 border-e border-b border-gray-300 ps-3 py-2">{{ $mapel->nama_mapel ?? 'N/A' }}</p>
                        @for ($i = 1; $i <= 5; $i++)
                            @php
                                $nilai = $siswa->semesters->where('mapel_id', $mapel->id)->where('semester', $i)->first();
                            @endphp
                            <p class="col-span-1 border-e border-b border-gray-300 py-2 text-center">
                                {{ $nilai->nilai_semester ?? '-' }}
                            </p>
                        @endfor
                    </div>
                @endforeach

                <div class="grid grid-cols-7 bg-gray-100 font-bold">
                    <p class="col-span-2 border-e border-t border-gray-300 ps-3 py-2">Nilai Akhir</p>
                    <p class="col-span-5 border-t border-gray-300 text-center py-2">{{ number_format($nilaiAkhir, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
