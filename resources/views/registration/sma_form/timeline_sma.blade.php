<x-app-layout>
    <div class="pt-2 px-4 lg:pb-0 pb-2 border-b border-gray-300 flex justify-between">
        <div class="flex items-center justify-start sm:p-4"> 
            <a href="{{ route('pendaftaran_sma') }}" 
                class="inline-flex items-center gap-1 sm:gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold 
                            text-sm sm:text-base px-2 py-1 sm:px-4 sm:py-2 rounded-md sm:rounded-lg shadow-sm sm:shadow-md 
                            transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" 
                        fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                        class="w-4 h-4 sm:w-5 sm:h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="hidden sm:inline">Kembali</span>
            </a>
            {{-- Pindahkan teks ini ke div yang sama --}}
            <p class="text-gray-500 ms-3 lg:hidden">Kembali ke daftar sekolah</p>
        </div>
        <div class="flex items-center gap-x-2 me-3 my-auto flex-shrink-0">
            
            {{-- Logo SMA --}}
            <div class="w-8 h-8 sm:w-10 sm:h-10">
                {{-- Mengakses logo melalui relasi $siswa->dataSma --}}
                @if ($siswa->dataSma->logo_sma ?? false) 
                    <img class="w-full h-full object-cover rounded-full" 
                        src="{{ asset('storage/' . $siswa->dataSma->logo_sma) }}" 
                        alt="Logo {{ $siswa->dataSma->nama_sma ?? 'SMA' }}">
                @else
                    {{-- Placeholder jika tidak ada logo --}}
                    <div class="w-full h-full bg-gray-200 rounded-full flex items-center justify-center">
                        <span class="text-xs text-gray-500">SMA</span>
                    </div>
                @endif
            </div>
            
            {{-- Nama SMA --}}
            <div class="hidden sm:block text-sm sm:text-base font-bold text-gray-800">
                {{ $siswa->dataSma->nama_sma ?? 'N/A' }}
            </div>
        </div>
    </div>

    <div class="py-2 lg:py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-4 lg:px-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                <form method="POST" action="{{ route('pendaftaran.sma.save_step') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="current_step" value="{{ $currentStep }}">

                    @if ($currentStep == 1)
                        @include('account.timeline_form.biodata')
                    @elseif ($currentStep == 2)
                        @include('account.timeline_form.rapor')
                    @elseif ($currentStep == 3)
                        @if ($jalurId == 1)
                            @include('registration.sma_form.sertifikat')
                        @elseif ($jalurId == 2)
                            @include('registration.sma_form.dokumen_afirmasi')
                        @else
                            @include('registration.sma_form.zonasi')
                        @endif
                    @elseif ($currentStep == 4)
                        @include('registration.sma_form.resume_sma')
                    @endif
                    
                    <div class="mt-4">
                        @include('registration.sma_form.pagination_sma', ['currentStep' => $currentStep])
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
