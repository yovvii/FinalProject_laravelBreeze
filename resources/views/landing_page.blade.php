<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang | Aplikasi Sekolah</title>
    @vite('resources/css/app.css')

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- NAVBAR -->
    <nav class="bg-white border-b border-gray-200 fixed w-full z-10 top-0 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="/assets/logo_app_2.png" alt="Logo" class="h-14 mt-1">
                <h1 class="text-lg sm:text-xl font-bold text-blue-600 mb-1 hidden md:block">| Sistem Penerimaan Murid Baru</h1>
            </div>

            <!-- Desktop menu -->
            <div class="hidden md:flex space-x-4 items-center">
                <a href="/login" class="text-gray-600 hover:text-blue-600 transition">Masuk</a>
                <a href="/register" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Daftar</a>
            </div>

            <!-- Mobile menu button -->
            <button id="menu-btn" class="md:hidden focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
            <a href="/login" class="block px-6 py-3 text-gray-700 hover:bg-gray-100 text-center">Masuk</a>
            <a href="/register" class="block px-6 py-3 text-blue-600 font-medium hover:bg-gray-50 text-center">Daftar</a>
        </div>
    </nav>

    <!-- HERO SECTION -->   
    <section class="pt-28 md:pt-30 pb-5 md:pb-5 bg-gradient-to-b from-white to-blue-50 text-center px-6">

        {{-- MOBILE VIEW --}}
        <div class="block md:hidden">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-xl font-bold mb-6 text-gray-800 leading-snug">
                    SPMB (Sistem Penerimaan Murid Baru) Untuk SMA Negeri Kabupaten Purbalingga
                </h1>
                <p class="text-gray-600 mb-8 text-sm">
                    Kelola data siswa, guru, dan administrasi sekolah dengan mudah dan cepat.  
                    Satu platform terpadu untuk semua kebutuhan sekolah Anda.
                </p>
            </div>

            {{-- SLIDER UNTUK MOBILE --}}
            <div 
                x-data="{ 
                    activeSlide: 0, 
                    slides: {{ $banners->count() }},
                    next() {
                        this.activeSlide = this.activeSlide === this.slides - 1 ? 0 : this.activeSlide + 1;
                    },
                    init() {
                        setInterval(() => { this.next(); }, 4000);
                    }
                }"
                class="relative w-full mx-auto rounded-xl overflow-hidden"
            >
                <div class="flex transition-transform duration-700 ease-in-out aspect-[19/6]"
                    :style="`transform: translateX(-${activeSlide * 100}%)`">
                    @foreach ($banners as $index => $banner)
                        <div class="w-full flex-shrink-0 relative">
                            <img src="{{ asset('storage/' . $banner->image_path) }}" 
                                alt="Banner {{ $index + 1 }}" 
                                class="absolute inset-0 w-full h-full object-cover">
                            @if ($banner->content && $banner->content !== 'Automatic upload via dashboard')
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center p-4">
                                    <p class="text-white font-extrabold text-xl text-center whitespace-pre-line">
                                        {{ $banner->content }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- DOTS --}}
                <div class="absolute bottom-3 left-0 right-0 flex justify-center space-x-2">
                    @foreach ($banners as $index => $banner)
                        <button @click="activeSlide = {{ $index }}" 
                            :class="{'bg-white': activeSlide === {{ $index }}, 'bg-gray-400': activeSlide !== {{ $index }} }"
                            class="w-2.5 h-2.5 rounded-full transition-colors duration-300">
                        </button>
                    @endforeach
                </div>
            </div>
        </div>


        {{-- DESKTOP VIEW --}}
        <div 
            x-data="{ 
                activeSlide: 0, 
                slides: {{ $banners->count() }},
                next() {
                    this.activeSlide = this.activeSlide === this.slides - 1 ? 0 : this.activeSlide + 1;
                },
                init() {
                    setInterval(() => { this.next(); }, 4000);
                }
            }" 
            class="relative hidden md:block w-full mx-auto rounded-xl overflow-hidden"
        >
            {{-- SLIDER GAMBAR --}}
            <div class="flex transition-transform duration-700 ease-in-out aspect-[19/6]"
                :style="`transform: translateX(-${activeSlide * 100}%)`">
                @foreach ($banners as $index => $banner)
                    <div class="w-full flex-shrink-0 relative">
                        <img src="{{ asset('storage/' . $banner->image_path) }}" 
                            alt="Banner {{ $index + 1 }}" 
                            class="absolute inset-0 w-full h-full object-cover">
                    </div>
                @endforeach
            </div>

            {{-- TEKS OVERLAY (DIAM, TIDAK SLIDE) --}}
            <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center px-6 text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 text-white leading-snug drop-shadow-lg">
                    (SPMB) Sistem Penerimaan Murid Baru Untuk SMA Negeri Kabupaten Purbalingga
                </h1>
                <p class="text-white/90 max-w-2xl text-lg drop-shadow-md">
                    Kelola data siswa, guru, dan administrasi sekolah dengan mudah dan cepat.  
                    Satu platform terpadu untuk semua kebutuhan sekolah Anda.
                </p>
            </div>

            {{-- DOTS --}}
            <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
                @foreach ($banners as $index => $banner)
                    <button 
                        @click="activeSlide = {{ $index }}" 
                        :class="{'bg-white': activeSlide === {{ $index }}, 'bg-gray-400': activeSlide !== {{ $index }} }"
                        class="w-3 h-3 rounded-full transition-colors duration-300">
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="py-16 md:py-20 bg-white">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-10">Mengapa Memilih Kami?</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="p-6 bg-gray-50 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="text-blue-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold mb-2">Cepat & Efisien</h3>
                    <p class="text-gray-600 text-sm md:text-base">Proses administrasi lebih cepat tanpa ribet.</p>
                </div>

                <!-- Feature 2 -->
                <div class="p-6 bg-gray-50 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="text-blue-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8a9 9 0 100-18 9 9 0 000 18z" />
                        </svg>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold mb-2">Mudah Digunakan</h3>
                    <p class="text-gray-600 text-sm md:text-base">Antarmuka sederhana, cocok untuk semua pengguna.</p>
                </div>

                <!-- Feature 3 -->
                <div class="p-6 bg-gray-50 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="text-blue-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.105.895-2 2-2s2 .895 2 2-2 2-2 2zM12 11v10m0-10l-8-8m8 8l8-8" />
                        </svg>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold mb-2">Terintegrasi</h3>
                    <p class="text-gray-600 text-sm md:text-base">Semua data sekolah terhubung dalam satu sistem.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-blue-600 text-white py-8">
        <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 text-center md:text-left">
            <p class="text-sm md:text-base">&copy; {{ date('Y') }} Pemerintah Kabupaten Purbalingga</p>
            <div class="space-x-4 text-sm md:text-base">
                <a href="#" class="hover:underline">Kebijakan Privasi</a>
                <a href="#" class="hover:underline">Bantuan</a>
            </div>
        </div>
    </footer>

    <!-- SCRIPT: Mobile Menu -->
    <script>
        const btn = document.getElementById('menu-btn');
        const menu = document.getElementById('mobile-menu');
        btn.addEventListener('click', () => menu.classList.toggle('hidden'));
    </script>

</body>
</html>
