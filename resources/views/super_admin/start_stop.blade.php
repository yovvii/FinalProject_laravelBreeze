@extends('super_admin.layouts.super_admin_layout')

@php
    // Variabel ini sudah dikirim dari Controller: $startingTime dan $closingTime
    $spmbStatus = \App\Models\SpmbStatus::first(); // Ambil lagi jika Anda butuh status
    $isClosed = $spmbStatus && $spmbStatus->status === 'closed';
    $startingTime = $spmbStatus ? $spmbStatus->starting_at : null;
    $closingTime = $spmbStatus ? $spmbStatus->closing_at : null;
    
    // Pastikan variabel ISO 8601 didefinisikan DI SINI
    $startingTimeIso = $startingTime ? $startingTime->toIso8601String() : null;
    $closingTimeIso = $closingTime ? $closingTime->toIso8601String() : null;
@endphp

@section('content')
<div class="p-6 bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen">
    <div class="mx-auto space-y-10">

        {{-- STATUS & JADWAL OTOMATIS --}}
        @php
            $spmbStatus = \App\Models\SpmbStatus::first();
            $isClosed = $spmbStatus && $spmbStatus->status === 'closed';
            $startingTime = $spmbStatus ? $spmbStatus->starting_at : null;
            $closingTime = $spmbStatus ? $spmbStatus->closing_at : null;
        @endphp

        {{-- 🔹 Blok Jadwal Otomatis --}}
        <div class="bg-white shadow-xl shadow-blue-100 rounded-2xl border border-blue-200 p-8 transition duration-300 hover:shadow-blue-200/60">
            <h3 class="text-2xl font-extrabold text-blue-900 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7 text-blue-800">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Pengaturan Jadwal SPMB Otomatis
            </h3>

            <p class="mt-2 text-gray-600 leading-relaxed">
                Tentukan waktu kapan <span class="font-semibold text-blue-800">pendaftaran SPMB</span> dibuka dan ditutup secara otomatis oleh sistem.
            </p>

            @if ($startingTime && $closingTime)
                <div class="mt-5 bg-gradient-to-br from-blue-50 via-blue-100 to-blue-50 border border-blue-200 rounded-2xl p-5 text-blue-900 shadow-inner relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-full bg-blue-100/30 blur-2xl"></div>

                    <div class="relative z-10">
                        <p class="font-semibold mb-2 text-blue-800 text-base">📅 Jadwal Aktif:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Mulai: <span class="font-bold">{{ $startingTime->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }} WIB</span></li>
                            <li>Selesai: <span class="font-bold">{{ $closingTime->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }} WIB</span></li>
                        </ul>

                        {{-- COUNTDOWN STYLISH --}}
                        <div id="countdown-container" class="mt-6 border-t border-blue-200 pt-5">
                            <p class="font-bold text-lg mb-3 text-center tracking-wide text-blue-900">
                                Hitungan Mundur
                            </p>

                            <div id="countdown-output" class="flex justify-center gap-4 sm:gap-6 text-center">
                                <div class="bg-white/70 backdrop-blur-md rounded-xl shadow px-4 py-3 w-20 sm:w-24 border border-blue-200 transition">
                                    <p id="days" class="text-2xl sm:text-3xl font-extrabold text-blue-900">00</p>
                                    <span class="block text-xs uppercase text-blue-600 tracking-wider">Hari</span>
                                </div>
                                <div class="bg-white/70 backdrop-blur-md rounded-xl shadow px-4 py-3 w-20 sm:w-24 border border-blue-200 transition">
                                    <p id="hours" class="text-2xl sm:text-3xl font-extrabold text-blue-900">00</p>
                                    <span class="block text-xs uppercase text-blue-600 tracking-wider">Jam</span>
                                </div>
                                <div class="bg-white/70 backdrop-blur-md rounded-xl shadow px-4 py-3 w-20 sm:w-24 border border-blue-200 transition">
                                    <p id="minutes" class="text-2xl sm:text-3xl font-extrabold text-blue-900">00</p>
                                    <span class="block text-xs uppercase text-blue-600 tracking-wider">Menit</span>
                                </div>
                                <div class="bg-white/70 backdrop-blur-md rounded-xl shadow px-4 py-3 w-20 sm:w-24 border border-blue-200 transition">
                                    <p id="seconds" class="text-2xl sm:text-3xl font-extrabold text-blue-900">00</p>
                                    <span class="block text-xs uppercase text-blue-600 tracking-wider">Detik</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <p class="mt-4 text-sm text-gray-500 italic">Belum ada jadwal pendaftaran yang diatur.</p>
            @endif

            {{-- FORM SET JADWAL --}}
            <form method="POST" action="{{ route('super_admin.ppdb.set_schedule') }}" class="mt-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Input Mulai --}}
                    <div>
                        <label for="starting_at" class="block text-sm font-semibold text-gray-700 mb-1">
                            Waktu Mulai SPMB
                        </label>
                        <input type="datetime-local" id="starting_at" name="starting_at"
                            value="{{ old('starting_at', $startingTime ? $startingTime->format('Y-m-d\TH:i') : '') }}"
                            required
                            class="w-full rounded-xl border-gray-300 focus:border-blue-600 focus:ring-blue-600 shadow-sm transition duration-150">
                        @error('starting_at')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input Selesai --}}
                    <div>
                        <label for="closing_at" class="block text-sm font-semibold text-gray-700 mb-1">
                            Waktu Selesai SPMB
                        </label>
                        <input type="datetime-local" id="closing_at" name="closing_at"
                            value="{{ old('closing_at', $closingTime ? $closingTime->format('Y-m-d\TH:i') : '') }}"
                            required
                            class="w-full rounded-xl border-gray-300 focus:border-blue-600 focus:ring-blue-600 shadow-sm transition duration-150">
                        @error('closing_at')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit"
                        class="w-full py-3 bg-blue-900 text-white font-semibold rounded-xl hover:bg-blue-800 active:scale-[0.98] transition duration-200">
                        💾 Simpan dan Jalankan Jadwal
                    </button>
                </div>
            </form>
        </div>

        {{-- 🔺 Blok Penghentian Manual --}}
        <div class="bg-white shadow-xl shadow-blue-100 rounded-2xl border border-red-200 p-8 transition duration-300 hover:shadow-red-200/60">
            <h3 class="text-2xl font-extrabold text-red-700 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6">
                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14ZM6.5 5.5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1h-3Z" clip-rule="evenodd" />
                </svg>
                Penghentian & Reset Manual
            </h3>

            <p class="mt-2 text-sm text-red-600 leading-relaxed">
                Gunakan fitur ini hanya jika kamu ingin <span class="font-bold">menghentikan SPMB sekarang juga</span> atau <span class="font-bold">mengatur ulang proses</span> tanpa menunggu jadwal otomatis.
            </p>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Tombol Stop --}}
                <form method="POST" action="{{ route('super_admin.ppdb.stop') }}"
                    onsubmit="return confirm('Yakin ingin menghentikan PPDB sekarang? Aksi ini akan menentukan hasil akhir penerimaan.');">
                    @csrf
                    <button type="submit"
                        class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 active:scale-[0.98] transition duration-200">
                        ⛔ Hentikan & Tentukan Penerimaan
                    </button>
                </form>

                {{-- Tombol Reset --}}
                <form method="POST" action="{{ route('super_admin.spmb.reset') }}"
                    onsubmit="return confirm('Yakin ingin mereset PPDB? Semua status penerimaan akan dihapus.');">
                    @csrf
                    <button type="submit"
                        class="w-full px-6 py-3 bg-gray-600 text-white font-semibold rounded-xl hover:bg-gray-700 active:scale-[0.98] transition duration-200">
                        🔁 Mulai / Reset PPDB
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@if ($startingTimeIso && $closingTimeIso)
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ambil waktu target dari variabel Blade yang diubah ke string ISO
        const START_TIME_ISO = "{{ $startingTimeIso }}";
        const END_TIME_ISO = "{{ $closingTimeIso }}";

        const startTarget = new Date(START_TIME_ISO).getTime();
        const endTarget = new Date(END_TIME_ISO).getTime();
        const countdownOutput = document.getElementById('countdown-output');

        if (!countdownOutput) return;

        function updateCountdown() {
            const now = new Date().getTime();
            let distance;
            let message = "";
            let status = "";
            let styleClass = "";

            if (now < startTarget) {
                // KONDISI 1: Belum mulai, hitung mundur menuju Waktu Mulai
                distance = startTarget - now;
                message = "SPMB Akan Dibuka dalam: ";
                status = "Mulai";
                styleClass = "bg-yellow-200 text-yellow-800";
            } else if (now >= startTarget && now < endTarget) {
                // KONDISI 2: Sudah mulai, hitung mundur menuju Waktu Selesai
                distance = endTarget - now;
                message = "SPMB Sedang Berlangsung. Akan Ditutup dalam: ";
                status = "Selesai";
                styleClass = "bg-green-200 text-green-800";
            } else {
                // KONDISI 3: Sudah selesai
                distance = 0;
                message = "Waktu Pendaftaran SUDAH BERAKHIR.";
                status = "Selesai";
                styleClass = "bg-red-200 text-red-800";
            }

            // Logika Format Tampilan
            if (distance > 0) {
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Tampilkan format lengkap
                countdownOutput.innerHTML = `
                    <p class="text-sm font-medium">${message}</p>
                    <span class="text-3xl">${days}h ${hours}j ${minutes}m ${seconds}d</span>
                `;
            } else {
                // Tampilkan pesan selesai
                countdownOutput.innerHTML = `<p class="text-xl font-bold">${message}</p>`;
            }
            
            // Atur class styling Tailwind
            countdownOutput.className = `text-xl font-extrabold text-center p-3 rounded-lg ${styleClass}`;
        }

        // Jalankan fungsi update setiap 1 detik
        const countdownInterval = setInterval(updateCountdown, 1000);
        
        // Panggil sekali saat dimuat untuk menghindari jeda 1 detik
        updateCountdown(); 
    });
</script>
@endpush
@endif
@endsection
