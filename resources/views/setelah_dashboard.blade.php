<x-app-layout>
    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="px-3 sm:px-5 md:px-0 mb-5">
                @if ($banners->isNotEmpty())
        
                    {{-- 🔥 ALPINE.JS CAROUSEL START --}}
                    <div x-data="{ 
                        activeSlide: 0, 
                        slides: {{ $banners->count() }},
                        
                        // Fungsi untuk pindah ke slide berikutnya
                        next() {
                            this.activeSlide = this.activeSlide === this.slides - 1 ? 0 : this.activeSlide + 1;
                        },
                        
                        // Pemicu slide otomatis
                        init() {
                            setInterval(() => {
                                this.next();
                            }, 4000); // Ganti slide setiap 4 detik (4000ms)
                        }
                    }" class="relative w-full mx-auto rounded-xl overflow-hidden">

                        {{-- Container Slider (Dengan tinggi tetap h-48) --}}
                        <div class="flex transition-transform duration-700 ease-in-out aspect-[19/6]"
                            :style="`transform: translateX(-${activeSlide * 100}%)`">
                            
                            @foreach ($banners as $index => $banner)
                                {{-- Setiap Slide --}}
                                <div class="w-full flex-shrink-0 relative"> 
                                    
                                    {{-- GAMBAR BANNER --}}
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" 
                                        alt="Banner {{ $index + 1 }}" 
                                        class="absolute inset-0 w-full h-full object-cover">
                                    
                                    {{-- TEKS OVERLAY (Jika konten ada dan bukan pesan default upload) --}}
                                    @if ($banner->content && $banner->content !== 'Automatic upload via dashboard')
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center p-4">
                                            <p class="text-white font-extrabold text-xl sm:text-2xl text-center whitespace-pre-line">
                                                {{ $banner->content }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Indikator Dots (Opsional) --}}
                        <div class="absolute bottom-3 left-0 right-0 flex justify-center space-x-2">
                            @foreach ($banners as $index => $banner)
                                <button @click="activeSlide = {{ $index }}" 
                                        :class="{'bg-white': activeSlide === {{ $index }}, 'bg-gray-400': activeSlide !== {{ $index }} }"
                                        class="w-2.5 h-2.5 rounded-full transition-colors duration-300"></button>
                            @endforeach
                        </div>

                    </div>
                    {{-- ALPINE.JS CAROUSEL END --}}
                    
                @else
                    {{-- Tampilan default jika belum ada banner --}}
                    <div class="text-center bg-blue-50 h-48 flex items-center justify-center rounded-lg border-4 border-blue-200 p-4">
                        <p class="text-blue-700 font-bold text-xl">Selamat Datang di Portal Penerimaan Siswa Baru</p>
                    </div>
                @endif
            </div>
            <div class="bg-white overflow-hidden lg:shadow-xl sm:rounded-lg mx-3 lg:mx-0 p-4">

                {{-- === Bagian Navigasi Cepat === --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    {{-- Kartu 1: Pendaftaran --}}
                    <a href="{{ route('pendaftaran_sma') }}" 
                    class="flex flex-col items-center justify-center p-4 bg-gradient-to-b from-blue-50 to-blue-100 border border-blue-200 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
                        <div class="bg-blue-600 text-white p-3 rounded-full mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M13 19c0 1.1.3 2.12.81 3H6c-1.11 0-2-.89-2-2V4a2 2 0 0 1 2-2h1v7l2.5-1.5L12 9V2h6a2 2 0 0 1 2 2v9.09c-.33-.05-.66-.09-1-.09c-3.31 0-6 2.69-6 6m10 0l-3-3v2h-4v2h4v2z"/>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-blue-700 text-lg">Pendaftaran</h4>
                        <p class="text-sm text-gray-600 text-center">Daftarkan diri kamu di SMA pilihanmu.</p>
                    </a>

                    {{-- Kartu 2: Alur Pendaftaran --}}
                    <a href="{{ route('alur.index') }}" 
                    class="flex flex-col items-center justify-center p-4 bg-gradient-to-b from-green-50 to-green-100 border border-green-200 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
                        <div class="bg-green-600 text-white p-3 rounded-full mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 6H7a2 2 0 01-2-2V6a2 2 0 012-2h7l5 5v13a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-green-700 text-lg">Alur Pendaftaran</h4>
                        <p class="text-sm text-gray-600 text-center">Pelajari langkah-langkah pendaftaran yang benar.</p>
                    </a>

                    {{-- Kartu 3: Juknis Pendaftaran --}}
                    <a href="{{ route('juknis.index') }}" 
                    class="flex flex-col items-center justify-center p-4 bg-gradient-to-b from-yellow-50 to-yellow-100 border border-yellow-200 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
                        <div class="bg-yellow-500 text-white p-3 rounded-full mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m0 0H8m4 0h4m-4-8h.01M4 4h16v16H4V4z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-yellow-700 text-lg">Juknis Pendaftaran</h4>
                        <p class="text-sm text-gray-600 text-center">Baca petunjuk teknis pendaftaran secara lengkap.</p>
                    </a>
                </div>

                {{-- === Bagian Informasi Pendaftaran === --}}
                <div class="text-gray-700 space-y-4 border border-gray-100 rounded-lg p-4">
                    <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">Rangkuman Pendaftaran Anda</h3>
                    <p>
                        <span class="font-bold w-48 inline-block">Sekolah Tujuan</span> 
                        <span class="text-blue-700">{{ $siswa->dataSma->nama_sma ?? 'N/A' }}</span>
                    </p>
                    <p>
                        <span class="font-bold w-48 inline-block">Jalur Pendaftaran</span> 
                        <span class="text-blue-700">{{ $siswa->jalurPendaftaran->nama_jalur_pendaftaran ?? 'N/A' }}</span>
                    </p>
                    <p>
                        <span class="font-bold w-48 inline-block">Status Pendaftaran</span> 
                        @php
                            $statusPenerimaan = strtolower($siswa->status_penerimaan ?? '');
                        @endphp

                        @if ($statusPenerimaan === 'diterima')
                            <span class="text-green-600 font-semibold underline">Diterima</span>
                        @elseif ($statusPenerimaan === 'ditolak')
                            <span class="text-red-600 font-semibold underline">Tidak Diterima</span>
                        @else
                            <span class="text-yellow-600 font-semibold underline">Dalam Tahap Seleksi</span>
                        @endif
                    </p>
                    <h3 class="text-xl font-semibold text-gray-800 pt-2">Informasi Penting</h3>
                    <p class="border-y py-2">
                        {!! nl2br(e($infoContent)) !!}
                    </p>
                    <p class="flex justify-between">
                        <span class="font-bold inline-block text-sm md:text-md">Status SPMB</span> 
                        -
                        @if ($selection_ended)
                            <span class="text-white font-semibold text-sm md:text-md bg-red-600 py-[1px] px-3 rounded-full">Ditutup</span>
                        @else
                            <span class="text-white font-semibold text-sm md:text-md bg-green-600 py-[1px] px-3 rounded-full">Sedang Berlangsung</span>
                        @endif
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>