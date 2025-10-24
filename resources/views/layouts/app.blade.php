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
        @stack('styles')
    </head>
    <body class="font-sans antialiased focus:outline-none">
        @php
            // 1. Dapatkan status global dari View Composer (bernilai true jika status 'closed')
            $pendaftaran_ditutup = $selection_ended ?? false; 
            $user = Auth::user();
            $show_restriction_card = false;

            if (Auth::check() && $user->role === 'siswa') {                
                $siswa_belum_mendaftar_sma = $user->siswa?->data_sma_id === null;
                $show_restriction_card = $pendaftaran_ditutup && $siswa_belum_mendaftar_sma;
            }
        @endphp

        @include('layouts.notification')

        @if (!$show_restriction_card)
            @include('layouts.navbar')
        @endif

        <div class="lg:flex min-h-screen bg-gray-100 dark:bg-gray-900">
            @if (!$show_restriction_card)
                @include('layouts.sidebar')
            @endif

            <main class="flex-1 oveflow-y-auto @if (!$show_restriction_card) mt-16 lg:mt-0 lg:ml-64 @endif">
                
                @if ($show_restriction_card)
                    @include('layouts.card_selesai')
                @else
                    {{ $slot }}
                @endif
            </main>
        </div>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                // 1. Definisikan fungsi SweetAlert agar bisa dipanggil oleh blok else if
                function showSwal(title, text, icon, buttonText = 'Tutup') {
                    Swal.fire({
                        title: title,
                        html: text,
                        icon: icon,
                        confirmButtonText: buttonText,
                        customClass: {
                            container: 'my-swal-container',
                        },
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                }

                // 2. 🔥 PERBAIKAN NAMA VARIABEL: $selection_ended yang dikirim dari View Composer 🔥
                const spmbStatus = @json(session('spmb_result_status'));
                const selectionEnded = @json($selection_ended ?? false); // Gunakan $selection_ended dari View Composer

                
                if (spmbStatus) {
                    let title = '';
                    let icon = '';
                    let text = '';
                    let buttonText = 'Tutup';
                    const status = spmbStatus.toLowerCase();

                    if (status === 'diterima') {
                        title = 'Selamat! Anda Diterima';
                        icon = 'success';
                        text = 'Berdasarkan hasil seleksi, Anda dinyatakan DITERIMA di sekolah tujuan. Silahkan tunggu informasi lebih lanjut.';
                        buttonText = 'Lanjutkan';
                    } else if (status === 'ditolak' || status === 'tidak diterima') {
                        title = 'Mohon Maaf';
                        icon = 'error';
                        text = 'Berdasarkan hasil seleksi, Anda dinyatakan TIDAK DITERIMA. Terima kasih telah berpartisipasi dalam proses pendaftaran.';
                        buttonText = 'Tutup';
                    }

                    if (title) {
                        // Tunda sebentar untuk memastikan SweetAlert sudah dimuat
                        setTimeout(() => {
                            showSwal(title, text, icon, buttonText);
                        }, 50);
                    }

                }
            </script>

        @stack('scripts')
    </body>
</html>
