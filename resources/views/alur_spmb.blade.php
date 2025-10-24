<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Alur SPMB') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl p-4">

                {{-- Asumsi: Variabel $alurPendaftaranPath berisi path file gambar. --}}
                @if (isset($alurPendaftaranPath) && $alurPendaftaranPath)
                    
                    @php
                        $imageUrl = asset('storage/' . $alurPendaftaranPath);
                        $imageName = \Illuminate\Support\Str::afterLast($alurPendaftaranPath, '/');
                        // Ambil ekstensi untuk nama file download
                        $extension = strtoupper(pathinfo($imageName, PATHINFO_EXTENSION));
                    @endphp

                    <div class="">
                        
                        {{-- Header dan Tombol Download --}}
                        <div class="md:flex hidden mb-6 flex-col sm:flex-row justify-between items-center p-4 bg-green-50 border border-green-200 rounded-lg">
                            <h3 class="text-lg font-bold text-green-700 dark:text-green-300 flex items-center mb-3 sm:mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-5 me-2">
                                <path fill-rule="evenodd" d="M15 8A7 7 0 1 1 1 8a7 7 0 0 1 14 0ZM8 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2ZM5.5 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm6 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                                </svg>
                                Diagram Alur Pendaftaran
                            </h3>
                            
                            {{-- Tombol Download Gambar --}}
                            <a href="{{ $imageUrl }}" download="alur_pendaftaran_{{ now()->format('Ymd') }}.{{ strtolower($extension) }}" target="_blank"
                               class="px-5 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition duration-150 transform hover:scale-[1.02]">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6 inline me-2">
                                    <path d="M8.75 2.75a.75.75 0 0 0-1.5 0v5.69L5.03 6.22a.75.75 0 0 0-1.06 1.06l3.5 3.5a.75.75 0 0 0 1.06 0l3.5-3.5a.75.75 0 0 0-1.06-1.06L8.75 8.44V2.75Z" />
                                    <path d="M3.5 9.75a.75.75 0 0 0-1.5 0v1.5A2.75 2.75 0 0 0 4.75 14h6.5A2.75 2.75 0 0 0 14 11.25v-1.5a.75.75 0 0 0-1.5 0v1.5c0 .69-.56 1.25-1.25 1.25h-6.5c-.69 0-1.25-.56-1.25-1.25v-1.5Z" />
                                </svg>
                                Download Gambar ({{ $extension }})
                            </a>
                        </div>

                        {{-- Display Gambar Alur Pendaftaran --}}
                        <div class="w-full bg-gray-50 dark:bg-gray-700 rounded-lg shadow-inner overflow-hidden border border-gray-200 dark:border-gray-700">
                            <h4 class="py-2 px-3 bg-gray-200 dark:bg-gray-700 text-black dark:text-gray-300 font-black text-lg border-b dark:border-gray-600">
                                Pratinjau Alur Pendaftaran
                            </h4>
                            <div class="p-4 flex justify-center">
                                <img src="{{ $imageUrl }}" 
                                     alt="Alur Pendaftaran SPMB" 
                                     class="max-w-full h-auto rounded-lg shadow-lg border dark:border-gray-600"
                                     style="max-height: 90vh;">
                            </div>
                        </div>

                        {{-- Header dan Tombol Download --}}
                        <div class="flex md:hidden mt-6 flex-col sm:flex-row justify-between items-center p-4 bg-green-50 border border-green-200 rounded-lg">
                            <h3 class="text-lg font-bold text-green-700 dark:text-green-300 flex items-center mb-3 sm:mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-5 me-2">
                                <path fill-rule="evenodd" d="M15 8A7 7 0 1 1 1 8a7 7 0 0 1 14 0ZM8 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2ZM5.5 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm6 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                                </svg>
                                Diagram Alur Pendaftaran
                            </h3>
                            
                            {{-- Tombol Download Gambar --}}
                            <a href="{{ $imageUrl }}" download="alur_pendaftaran_{{ now()->format('Ymd') }}.{{ strtolower($extension) }}" target="_blank"
                               class="px-5 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition duration-150 transform hover:scale-[1.02]">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6 inline me-2">
                                    <path d="M8.75 2.75a.75.75 0 0 0-1.5 0v5.69L5.03 6.22a.75.75 0 0 0-1.06 1.06l3.5 3.5a.75.75 0 0 0 1.06 0l3.5-3.5a.75.75 0 0 0-1.06-1.06L8.75 8.44V2.75Z" />
                                    <path d="M3.5 9.75a.75.75 0 0 0-1.5 0v1.5A2.75 2.75 0 0 0 4.75 14h6.5A2.75 2.75 0 0 0 14 11.25v-1.5a.75.75 0 0 0-1.5 0v1.5c0 .69-.56 1.25-1.25 1.25h-6.5c-.69 0-1.25-.56-1.25-1.25v-1.5Z" />
                                </svg>
                                Download Gambar ({{ $extension }})
                            </a>
                        </div>
                    </div>
                @else
                    {{-- Pesan jika Alur Pendaftaran belum diupload Admin --}}
                    <div class="p-6 text-center bg-yellow-100 border border-yellow-300 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mx-auto text-yellow-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.254-.238 3.331 1.064 4.143 1.35.857 3.12 1.107 4.965.915a11.597 11.597 0 005.518-.328 11.597 11.597 0 005.518.328c1.845.192 3.615-.058 4.965-.915 1.302-.812 1.93-2.89 1.064-4.143m-21.365 0a24.168 24.168 0 01.52-3.82c.162-.511.385-.992.664-1.442C3.172 10.45 4.312 9.4 6.136 9.4h11.728c1.824 0 2.964 1.05 3.73 2.114.279.45.502.931.664 1.442.17.537.288 1.09.351 1.644M12 9.75a.75.75 0 01-.75-.75V8.25m0 0h.007" />
                        </svg>
                        <p class="mt-4 text-xl font-semibold text-yellow-700">Alur Pendaftaran Belum Tersedia</p>
                        <p class="mt-1 text-yellow-600">Admin belum mengunggah diagram atau infografis Alur Pendaftaran.</p>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</x-app-layout>
