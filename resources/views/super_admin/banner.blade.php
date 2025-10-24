@extends('super_admin.layouts.super_admin_layout')

@section('content')
    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <form id="bannerUploadForm" method="POST" action="{{ route('banner.add') }}" enctype="multipart/form-data" class="hidden">
                @csrf
                {{-- Input file yang memicu kotak dialog OS --}}
                <input type="file" name="image" id="bannerFileUploader" accept="image/jpeg,image/png,image/jpg" required>
                {{-- Konten teks opsional, dikirim otomatis --}}
                <input type="hidden" name="content" value="Automatic upload via dashboard"> 
            </form>

            {{-- 2. TOMBOL VISUAL (Pemicu Upload) --}}
            <div id="uploadButton" 
            class="w-full bg-blue-800 rounded-full py-2 flex items-center justify-center gap-x-1 
                    hover:bg-blue-900 transition duration-150 ease-in-out cursor-pointer mb-3"
            role="button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="white" class="size-6">
                    <path fill-rule="evenodd" d="M3.5 2A1.5 1.5 0 0 0 2 3.5v9A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5v-7A1.5 1.5 0 0 0 12.5 4H9.621a1.5 1.5 0 0 1-1.06-.44L7.439 2.44A1.5 1.5 0 0 0 6.38 2H3.5ZM8 6a.75.75 0 0 1 .75.75v1.5h1.5a.75.75 0 0 1 0 1.5h-1.5v1.5a.75.75 0 0 1-1.5 0v-1.5h-1.5a.75.75 0 0 1 0-1.5h1.5v-1.5A.75.75 0 0 1 8 6Z" clip-rule="evenodd" />
                </svg>
                <p class="font-poppins font-semibold text-white text-lg">Tambah Banner</p>
            </div>

            @if ($banners->isEmpty())
                <div class="p-6 text-center border-2 border-dashed border-gray-300 rounded-lg">
                    <p class="text-gray-500">Belum ada banner yang terunggah. Silakan klik tombol "Tambah Banner" di atas untuk mengunggah.</p>
                </div>
            @else
                {{-- 🔥 Lakukan looping untuk setiap banner --}}
                <div class="grid gap-y-3">
                    @foreach ($banners as $banner)
                        <div class="relative rounded-xl overflow-hidden shadow-lg border border-gray-100">
                            <img src="{{ asset('storage/' . $banner->image_path) }}" 
                                alt="Banner ID {{ $banner->id }}" 
                                class="w-full h-48 object-cover">

                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-blue-700/60 to-blue-900/90 flex items-end justify-end p-4 space-x-2 rounded-xl shadow-xl">
                                
                                {{-- Tombol LIHAT --}}
                                <button onclick="window.open('{{ asset('storage/' . $banner->image_path) }}', '_blank')"
                                        class="flex items-center gap-2 bg-white text-blue-900 px-3 py-1.5 rounded-full text-sm font-medium shadow-sm hover:bg-gray-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6">
                                    <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" />
                                    <path fill-rule="evenodd" d="M1.38 8.28a.87.87 0 0 1 0-.566 7.003 7.003 0 0 1 13.238.006.87.87 0 0 1 0 .566A7.003 7.003 0 0 1 1.379 8.28ZM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-lg">Lihat</p>
                                </button>

                                {{-- Tombol HAPUS --}}
                                <form method="POST" action="{{ route('banner.delete', $banner->id) }}" 
                                    onsubmit="return confirm('Yakin mau hapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="flex items-center gap-2 bg-red-600 text-white px-3 py-1.5 rounded-full text-sm font-medium shadow-sm hover:bg-red-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M5 3.25V4H2.75a.75.75 0 0 0 0 1.5h.3l.815 8.15A1.5 1.5 0 0 0 5.357 15h5.285a1.5 1.5 0 0 0 1.493-1.35l.815-8.15h.3a.75.75 0 0 0 0-1.5H11v-.75A2.25 2.25 0 0 0 8.75 1h-1.5A2.25 2.25 0 0 0 5 3.25Zm2.25-.75a.75.75 0 0 0-.75.75V4h3v-.75a.75.75 0 0 0-.75-.75h-1.5ZM6.05 6a.75.75 0 0 1 .787.713l.275 5.5a.75.75 0 0 1-1.498.075l-.275-5.5A.75.75 0 0 1 6.05 6Zm3.9 0a.75.75 0 0 1 .712.787l-.275 5.5a.75.75 0 0 1-1.498-.075l.275-5.5a.75.75 0 0 1 .786-.711Z" clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-lg">Hapus</p>
                                    </button>
                                </form>
                                
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Bagian Catatan Gambar Upload --}}
            <div class="bg-gradient-to-r from-blue-50 via-white to-blue-50 border border-blue-200 shadow-sm w-full p-4 mt-4 rounded-b-3xl">
                <h3 class="text-blue-900 font-semibold text-xl mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14ZM8 4a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 4Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                    </svg>
                    Catatan Penting
                </h3>
                <p class="text-gray-700 text-md">Pastikan gambar yang diupload memiliki rasio <span class="font-semibold">19:6</span> untuk tampilan yang optimal di website.</p>
                <p class="text-gray-500 text-sm mt-1">Ukuran file ideal <span class="font-medium">≤ 2MB</span>. Format yang disarankan: <span class="font-medium">JPG, PNG</span>.</p>
            </div>

        </div>
    </div>
    @push('scripts')
    <script>
        document.getElementById('uploadButton').addEventListener('click', function() {
            // Memicu klik pada input file tersembunyi, yang akan membuka dialog OS
            document.getElementById('bannerFileUploader').click();
        });

        document.getElementById('bannerFileUploader').addEventListener('change', function() {
            // Setelah file dipilih, submit form secara otomatis
            if (this.files.length > 0) {
                document.getElementById('bannerUploadForm').submit();
            }
        });
    </script>
    @endpush
@endsection