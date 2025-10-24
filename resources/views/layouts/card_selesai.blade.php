<div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-lg shadow-2xl max-w-xl w-full text-center border-t-4 border-red-600">
        <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
        <h2 class="text-2xl font-extrabold text-gray-900 mt-4">Pendaftaran Telah Ditutup</h2>
        <p class="text-gray-600 mt-2">
            Tahap pendaftaran PPDB online telah <span class="font-bold text-black underline">resmi berakhir</span>.
            Mohon maaf, Anda tidak dapat lagi mengakses menu pendaftaran karena Anda <span class="font-bold text-black">belum memilih sekolah tujuan</span> sebelum batas waktu.
        </p>
        <p class="text-gray-600 mt-4 font-semibold">
            Untuk informasi hasil seleksi akhir, silakan kunjungi halaman utama PPDB.
        </p>
        {{-- Pastikan rute 'landing' sudah terdefinisi --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-[50%] flex items-center mx-auto mt-3 px-3 py-2 rounded-lg hover:bg-blue-600 bg-blue-700">
                <span class="text-center text-white mx-auto">Kembali Ke Halaman Utama</span>
            </button>
        </form>
    </div>
</div>