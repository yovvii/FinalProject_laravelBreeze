@extends('super_admin.layouts.super_admin_layout')

@section('content')
<div class="p-5">
    <div class="mx-auto">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-blue-100">
            
            {{-- HEADER --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-500 py-6 px-6 text-center text-white">
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-wide">
                    Pengaturan Batas Usia Siswa
                </h2>
                <p class="text-blue-100 text-sm mt-1 font-light">
                    Sesuaikan ketentuan batas usia pendaftaran secara real-time
                </p>
            </div>

            {{-- ISI --}}
            <div class="p-8 sm:p-10 max-w-3xl m-auto">
                
                {{-- Judul Tengah --}}
                <div class="text-center mb-8">
                    <p class="inline-block px-6 py-2 bg-blue-50 border border-blue-500 text-blue-700 
                        font-poppins font-bold text-xl rounded-full shadow-sm">
                        Batas Ketentuan Usia
                    </p>
                </div>

                {{-- Tampilan Rentang Usia --}}
                <div class="flex flex-col items-center justify-center mb-8">
                    <p class="text-blue-700 font-black text-5xl sm:text-7xl flex items-center gap-3">
                        <span class="text-gray-700 text-lg font-medium">Selang</span>
                        {{ $ageLimit->min_age_years }}
                        <span class="text-gray-500 text-4xl font-semibold">-</span>
                        {{ $ageLimit->max_age_years }}
                        <span class="text-gray-700 text-lg font-medium">Tahun</span>
                    </p>
                    <p class="text-gray-500 text-sm mt-3 italic">
                        *Usia dihitung per tanggal 
                        <span class="font-semibold text-gray-700">
                            {{ \Carbon\Carbon::parse($ageLimit->reference_date)->isoFormat('D MMMM YYYY') }}
                        </span>
                    </p>
                </div>

                {{-- FORM --}}
                <form action="{{ route('usia.siswa.update') }}" method="POST" class="space-y-8">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Minimum --}}
                        <div>
                            <label for="min_age_years" class="block text-sm font-semibold text-gray-700 mb-2">
                                Usia Minimum (Tahun)
                            </label>
                            <input type="number" name="min_age_years" id="min_age_years"
                                value="{{ old('min_age_years', $ageLimit->min_age_years) }}" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-800 
                                shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-100 transition">
                            @error('min_age_years')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Maksimum --}}
                        <div>
                            <label for="max_age_years" class="block text-sm font-semibold text-gray-700 mb-2">
                                Usia Maksimum (Tahun)
                            </label>
                            <input type="number" name="max_age_years" id="max_age_years"
                                value="{{ old('max_age_years', $ageLimit->max_age_years) }}" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-800 
                                shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-100 transition">
                            @error('max_age_years')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Tanggal Acuan --}}
                    <div class="flex flex-col items-center">
                        <div class="w-full md:w-2/3">
                            <label for="reference_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Acuan Perhitungan Usia
                            </label>
                            <input type="date" name="reference_date" id="reference_date"
                                value="{{ old('reference_date', $ageLimit->reference_date ? $ageLimit->reference_date->format('Y-m-d') : '') }}" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-800 
                                shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-100 transition">
                            @error('reference_date')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="flex justify-center pt-6">
                        <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold 
                            rounded-xl px-8 py-3 shadow-md transition-transform transform hover:scale-105">
                            Perbarui Ketentuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
