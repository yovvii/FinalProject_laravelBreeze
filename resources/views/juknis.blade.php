<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Juknis Pendaftaran') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl p-4">

                {{-- Asumsi: $juknisPath berisi path file PDF --}}
                @if (isset($juknisPath) && $juknisPath)
                    @php
                        $fileUrl = asset('storage/' . $juknisPath);
                        $fileName = \Illuminate\Support\Str::afterLast($juknisPath, '/');
                    @endphp

                    {{-- Header dan tombol download --}}
                    <div class=" hidden mb-6 md:flex flex-col sm:flex-row justify-between items-center gap-4 bg-blue-50 border border-blue-200 rounded-xl p-2 sm:p-3">
                        <div class="flex items-center text-blue-700 dark:text-blue-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-5 mr-3">
                            <path d="M2 3.5A1.5 1.5 0 0 1 3.5 2h2.879a1.5 1.5 0 0 1 1.06.44l1.122 1.12A1.5 1.5 0 0 0 9.62 4H12.5A1.5 1.5 0 0 1 14 5.5v1.401a2.986 2.986 0 0 0-1.5-.401h-9c-.546 0-1.059.146-1.5.401V3.5ZM2 9.5v3A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5v-3A1.5 1.5 0 0 0 12.5 8h-9A1.5 1.5 0 0 0 2 9.5Z" />
                            </svg>
                            <h3 class="text-md md:text-lg font-bold">Dokumen Petunjuk Teknis <span class="hidden md:inline text-gray-800">(Juknis)</span></h3>
                        </div>

                        {{-- Tombol download --}}
                        <a href="{{ $fileUrl }}" download="{{ $fileName }}" target="_blank"
                           class="w-full sm:w-auto text-center px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 transition transform hover:scale-[1.02] items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6 inline me-2">
                                <path d="M8.75 2.75a.75.75 0 0 0-1.5 0v5.69L5.03 6.22a.75.75 0 0 0-1.06 1.06l3.5 3.5a.75.75 0 0 0 1.06 0l3.5-3.5a.75.75 0 0 0-1.06-1.06L8.75 8.44V2.75Z" />
                                <path d="M3.5 9.75a.75.75 0 0 0-1.5 0v1.5A2.75 2.75 0 0 0 4.75 14h6.5A2.75 2.75 0 0 0 14 11.25v-1.5a.75.75 0 0 0-1.5 0v1.5c0 .69-.56 1.25-1.25 1.25h-6.5c-.69 0-1.25-.56-1.25-1.25v-1.5Z" />
                            </svg>
                            Download ({{ strtoupper(pathinfo($fileName, PATHINFO_EXTENSION)) }})
                        </a>
                    </div>

                    {{-- Pratinjau Dokumen --}}
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-xl shadow-inner border border-gray-200 dark:border-gray-600">
                        <div class="px-4 py-2 bg-gray-200 dark:bg-gray-800 border-b dark:border-gray-700 rounded-t-xl">
                            <h4 class="text-gray-800 dark:text-gray-300 font-semibold text-base sm:text-lg">
                                Pratinjau Dokumen
                            </h4>
                        </div>

                        {{-- Responsive PDF container --}}
                        <div class="relative w-full pt-[130%] sm:pt-[90%] lg:pt-[60%]">
                            <iframe src="{{ $fileUrl }}" 
                                    class="absolute top-0 left-0 w-full h-full rounded-b-xl"
                                    frameborder="0">
                                Dokumen PDF tidak dapat dimuat di browser Anda. Silakan klik tombol Download di atas.
                            </iframe>
                        </div>
                    </div>

                    {{-- Footer dan tombol download --}}
                    <div class=" md:hidden mt-6 flex flex-col sm:flex-row justify-between items-center gap-4 bg-blue-50 border border-blue-200 rounded-xl p-4 sm:p-5">
                        <div class="flex items-center text-blue-700 dark:text-blue-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6 mr-3">
                            <path d="M2 3.5A1.5 1.5 0 0 1 3.5 2h2.879a1.5 1.5 0 0 1 1.06.44l1.122 1.12A1.5 1.5 0 0 0 9.62 4H12.5A1.5 1.5 0 0 1 14 5.5v1.401a2.986 2.986 0 0 0-1.5-.401h-9c-.546 0-1.059.146-1.5.401V3.5ZM2 9.5v3A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5v-3A1.5 1.5 0 0 0 12.5 8h-9A1.5 1.5 0 0 0 2 9.5Z" />
                            </svg>
                            <h3 class="text-md md:text-xl font-bold">Dokumen Petunjuk Teknis <span class="hidden md:inline text-gray-800">(Juknis)</span></h3>
                        </div>

                        {{-- Tombol download --}}
                        <a href="{{ $fileUrl }}" download="{{ $fileName }}" target="_blank"
                           class="w-full sm:w-auto text-center px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 transition transform hover:scale-[1.02] items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6 inline me-2">
                                <path d="M8.75 2.75a.75.75 0 0 0-1.5 0v5.69L5.03 6.22a.75.75 0 0 0-1.06 1.06l3.5 3.5a.75.75 0 0 0 1.06 0l3.5-3.5a.75.75 0 0 0-1.06-1.06L8.75 8.44V2.75Z" />
                                <path d="M3.5 9.75a.75.75 0 0 0-1.5 0v1.5A2.75 2.75 0 0 0 4.75 14h6.5A2.75 2.75 0 0 0 14 11.25v-1.5a.75.75 0 0 0-1.5 0v1.5c0 .69-.56 1.25-1.25 1.25h-6.5c-.69 0-1.25-.56-1.25-1.25v-1.5Z" />
                            </svg>
                            Download ({{ strtoupper(pathinfo($fileName, PATHINFO_EXTENSION)) }})
                        </a>
                    </div>
                @else
                    {{-- Jika Juknis belum diunggah --}}
                    <div class="p-8 text-center bg-yellow-50 border border-yellow-300 rounded-xl shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto text-yellow-600 fill-current" viewBox="0 0 16 16">
                            <path d="M3.75 2a.75.75 0 0 0-.75.75v10.5a.75.75 0 0 0 1.28.53L8 10.06l3.72 3.72a.75.75 0 0 0 1.28-.53V2.75a.75.75 0 0 0-.75-.75h-8.5Z" />
                        </svg>
                        <p class="mt-4 text-lg sm:text-xl font-semibold text-yellow-700">Juknis Belum Tersedia</p>
                        <p class="mt-1 text-yellow-600 text-sm sm:text-base">Admin belum mengunggah dokumen Petunjuk Teknis pendaftaran.</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
