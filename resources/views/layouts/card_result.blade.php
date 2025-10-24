@php
    // KOREKSI: Tambahkan isset() untuk mencegah error "Undefined variable" jika variabel tidak dilewatkan.
    $is_accepted = isset($is_accepted) ? $is_accepted : false;
@endphp

{{-- CONTAINER OVERLAY: Menggunakan fixed inset-0, z-50, dan Alpine.js untuk kontrol modal --}}
<div id="result-modal" 
     x-data="{ open: true }" 
     x-show="open" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-75 backdrop-blur-sm">
    
    @if ($is_accepted)
        {{-- KARTU DITERIMA (HIJAU) --}}
        <div class="bg-white p-8 rounded-lg shadow-2xl max-w-xl w-full text-center border-t-4 border-green-600 relative">
            
            {{-- Tombol Tutup Card (WAJIB DITAMBAHKAN dismissResultCard()) --}}
            <button @click="open = false; dismissResultCard()" 
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h2 class="text-2xl font-extrabold text-gray-900 mt-4">🎉 SELAMAT! ANDA DINYATAKAN DITERIMA! 🎉</h2>
            <p class="text-gray-600 mt-2">
                Anda berhasil lolos seleksi. Silakan cek detail selanjutnya di halaman dashboard.
            </p>
            
            {{-- Tombol Aksi (WAJIB DITAMBAHKAN dismissResultCard()) --}}
            <button @click="open = false; dismissResultCard()" type="button" class="w-[50%] flex items-center mx-auto mt-3 px-3 py-2 rounded-lg hover:bg-green-700 bg-green-600">
                <span class="text-center text-white mx-auto">Lanjut ke Dashboard Siswa</span>
            </button>
        </div>
        
    @else
        {{-- KARTU DITOLAK (MERAH) --}}
        <div class="bg-white p-8 rounded-lg shadow-2xl max-w-xl w-full text-center border-t-4 border-red-600 relative">
            
            {{-- Tombol Tutup Card (WAJIB DITAMBAHKAN dismissResultCard()) --}}
            <button @click="open = false; dismissResultCard()" 
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h2 class="text-2xl font-extrabold text-gray-900 mt-4">Mohon Maaf.</h2>
            <p class="text-gray-600 mt-2">
                Anda **belum** dinyatakan diterima pada periode ini.
            </p>
            <p class="text-gray-600 mt-4 font-semibold">
                Untuk informasi lebih lanjut, silakan hubungi panitia PPDB.
            </p>
            
            {{-- Tombol Tutup (WAJIB DITAMBAHKAN dismissResultCard()) --}}
            <button @click="open = false; dismissResultCard()" type="button" class="w-[50%] flex items-center mx-auto mt-3 px-3 py-2 rounded-lg hover:bg-gray-700 bg-gray-600">
                <span class="text-center text-white mx-auto">Tutup Notifikasi</span>
            </button>
        </div>
    @endif

</div>