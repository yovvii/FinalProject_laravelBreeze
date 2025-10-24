<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">

        @include('layouts.notification')

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-blue-100 dark:bg-gray-900">
            {{-- NAVBAR: tetap di atas, tanpa logo (logo ada di konten) --}}
            <nav class="bg-white/80 backdrop-blur-sm border-b border-gray-200 fixed w-full z-50 top-0">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="h-14 flex items-center justify-between">

                        {{-- Left: Tombol Kembali --}}
                        <div>
                            <a href="{{ route('landing_page') }}" aria-label="Kembali ke landing page"
                            class="inline-flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-600 font-medium px-3 py-2 rounded-full shadow-sm transition transform hover:-translate-y-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="hidden sm:inline">Kembali</span>
                            </a>
                        </div>

                        {{-- Right: Judul + Dropdown Masuk Sebagai --}}
                        <div class="flex items-center gap-2">
                            <h1 class="hidden md:block text-sm md:text-base font-semibold text-blue-600">
                                Sistem Penerimaan Murid Baru |
                            </h1>

                            {{-- Tampilkan Role Aktif Berdasarkan Route --}}
                            @php
                                $currentRole = 'Tamu';

                                if (Route::is('superadmin.*')) {
                                    $currentRole = 'Super Admin';
                                } elseif (Route::is('admin.*')) {
                                    $currentRole = 'Admin Sekolah';
                                } elseif (Route::is('login') || Route::is('siswa.*')) {
                                    $currentRole = 'Siswa';
                                }
                            @endphp

                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open"
                                    class="flex items-center gap-1 text-gray-700 hover:text-blue-600 font-medium focus:outline-none">
                                    <span>
                                        @if ($currentRole === 'Tamu')
                                            Masuk sebagai
                                        @else
                                            {{ $currentRole }}
                                        @endif
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                {{-- Dropdown Menu --}}
                                <div x-show="open"
                                    @click.outside="open = false"
                                    x-transition
                                    class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg ring-1 ring-gray-200 z-50 py-2">
                                    <a href="{{ route('superadmin.login.form') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                        Super Admin
                                    </a>
                                    <a href="{{ route('admin.login') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                        Admin Sekolah
                                    </a>
                                    <a href="{{ route('login') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                        Siswa
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </nav>

            
            <div>
                <img src="{{ asset('assets/logo_app_2.png') }}" 
                    alt="Logo Aplikasi" 
                    class="md:w-[20%] w-[50%] md:mt-0 mt-[30%] object-contain mx-auto" />
            </div>

            <div class="w-full mx-5 sm:mx-0 max-w-sm sm:max-w-md mt-[1%] md:mt-0 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden rounded-3xl sm:rounded-3xl text-poppins">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
