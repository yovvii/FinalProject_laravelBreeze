<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil - SPMB</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />
</head>

<body class="bg-gray-100 font-poppins text-gray-800">
    @include('layouts.notification')

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

        {{-- Tombol Kembali --}}
        <div class="mb-6">
            <a href="{{ route('setelah.dashboard.show') }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg 
                font-semibold text-xs sm:text-sm text-gray-700 uppercase tracking-widest 
                shadow-sm hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>

        {{-- Kartu Utama --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Pengaturan Profil</h2>
                <p class="mt-1 text-sm text-gray-600">Lihat dan perbarui data pendaftaran Anda di sini.</p>
            </div>

            <div class="p-6 space-y-6">

                {{-- Ringkasan Data Siswa --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6 border-b pb-6">
                    <div class="flex justify-center mb-4 sm:mb-0">
                        <img src="{{ $siswa && $siswa->foto ? asset('storage/' . $siswa->foto) : asset('storage/profile_murid/avatar_empty.jpg') }}"
                            alt="Foto Profil"
                            class="h-28 w-28 rounded-full object-cover shadow-md ring-4 ring-blue-100">
                    </div>
                    <div class="text-center sm:text-left">
                        <h3 class="text-xl font-extrabold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">NISN: {{ $siswa->nisn ?? 'Belum ada' }}</p>
                        <p class="text-sm text-gray-500">Email: {{ $user->email }}</p>
                    </div>
                </div>

                {{-- Menu Aksi --}}
                <div class="space-y-4">
                    {{-- Biodata --}}
                    @include('profile.partials.profile_action_menu', [
                        'title' => 'Biodata dan Data Orang Tua',
                        'description' => 'Nama lengkap, alamat, NIK, dan koordinat rumah.',
                        'editRoute' => 'profile.edit.biodata',
                        'resetRoute' => 'profile.reset.biodata',
                        'isRegistered' => $isRegistered,
                    ])

                    {{-- Nilai --}}
                    @include('profile.partials.profile_action_menu', [
                        'title' => 'Nilai Rapor dan Mata Pelajaran',
                        'description' => 'Input nilai rapor semester 1 - 5 (jika diperlukan).',
                        'editRoute' => 'profile.edit.nilai',
                        'resetRoute' => 'profile.reset.nilai',
                        'isRegistered' => $isRegistered,
                    ])

                    {{-- Dokumen --}}
                    @include('profile.partials.profile_action_menu', [
                        'title' => 'Dokumen Pendukung',
                        'description' => 'Akta lahir, SKL/Ijazah, dan dokumen lainnya.',
                        'editRoute' => 'profile.edit.dokumen',
                        'resetRoute' => 'profile.reset.dokumen',
                        'isRegistered' => $isRegistered,
                    ])
                </div>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
