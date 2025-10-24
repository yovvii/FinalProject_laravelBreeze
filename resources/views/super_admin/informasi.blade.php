@extends('super_admin.layouts.super_admin_layout')

@section('content')
    <div class="py-5">
        <div class="mx-auto sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 sm:p-8 border-t-4 border-blue-600">
                
                <div class="flex items-center space-x-3 mb-6 border-b pb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6">
                    <path d="M12.613 1.258a1.535 1.535 0 0 1 2.13 2.129l-1.905 2.856a8 8 0 0 1-3.56 2.939 4.011 4.011 0 0 0-2.46-2.46 8 8 0 0 1 2.94-3.56l2.855-1.904ZM5.5 8A2.5 2.5 0 0 0 3 10.5a.5.5 0 0 1-.7.459.75.75 0 0 0-.983 1A3.5 3.5 0 0 0 8 10.5 2.5 2.5 0 0 0 5.5 8Z" />
                    </svg>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800">Pengaturan Informasi Penting</h1>
                        <p class="text-sm text-gray-500">Konten ini akan ditampilkan di dashboard Siswa.</p>
                    </div>
                </div>
                
                <form action="{{ route('super_admin.informasi.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- 🔥 Blok Upload PDF Juknis --}}
                    <div class="mb-6 pb-4 border-b">
                        <label for="juknis_pdf" class="block text-base font-semibold text-gray-700 mb-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="orange" class="size-4 me-2">
                            <path fill-rule="evenodd" d="M3.5 2A1.5 1.5 0 0 0 2 3.5v9A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5v-7A1.5 1.5 0 0 0 12.5 4H9.621a1.5 1.5 0 0 1-1.06-.44L7.439 2.44A1.5 1.5 0 0 0 6.38 2H3.5Zm6.75 7.75a.75.75 0 0 0 0-1.5h-4.5a.75.75 0 0 0 0 1.5h4.5Z" clip-rule="evenodd" />
                            </svg>
                            Dokumen Petunjuk Teknis (Juknis)
                        </label>
                        
                        {{-- Tampilkan file yang sudah ada --}}
                        @if ($setting->juknis_pdf_path)
                            <p class="text-sm text-gray-600 mb-2">
                                File saat ini: 
                                <a href="{{ asset('storage/' . $setting->juknis_pdf_path) }}" target="_blank" class="text-blue-600 hover:underline font-medium">
                                    {{ Str::afterLast($setting->juknis_pdf_path, '/') }} 
                                </a>
                                (Akan diganti jika Anda upload file baru)
                            </p>
                        @else
                            <p class="text-sm text-gray-500 mb-2">Belum ada dokumen Juknis yang diunggah.</p>
                        @endif

                        <input type="file" name="juknis_pdf" id="juknis_pdf" accept="application/pdf"
                               class="mt-1 block w-full text-sm text-gray-500 
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100"/>
                        @error('juknis_pdf') 
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="mb-6 pb-4 border-b">
                        <label for="alur_pendaftaran" class="block text-base font-semibold text-gray-700 mb-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="green" class="size-4 me-2">
                            <path d="M13.975 6.5c.028.276-.199.5-.475.5h-4a.5.5 0 0 1-.5-.5v-4c0-.276.225-.503.5-.475A5.002 5.002 0 0 1 13.974 6.5Z" />
                            <path d="M6.5 4.025c.276-.028.5.199.5.475v4a.5.5 0 0 0 .5.5h4c.276 0 .503.225.475.5a5 5 0 1 1-5.474-5.475Z" />
                            </svg>
                            Gambar Alur Pendaftaran (Diagram / Infografis)
                        </label>
                        
                        {{-- Tampilkan gambar yang sudah ada --}}
                        @if ($setting->alur_pendaftaran_path)
                            <p class="text-sm text-gray-600 mb-2">
                                Gambar saat ini: 
                                <a href="{{ asset('storage/' . $setting->alur_pendaftaran_path) }}" target="_blank" class="text-green-600 hover:underline font-medium">
                                    Lihat Gambar
                                </a>
                                (Akan diganti jika Anda upload file baru)
                            </p>
                            <img src="{{ asset('storage/' . $setting->alur_pendaftaran_path) }}" alt="Alur Pendaftaran" class="mt-2 mb-4 max-h-48 object-contain border rounded-lg shadow-sm">
                        @else
                            <p class="text-sm text-gray-500 mb-2">Belum ada gambar alur pendaftaran yang diunggah.</p>
                        @endif

                        <input type="file" name="alur_pendaftaran" id="alur_pendaftaran" accept="image/jpeg, image/png, image/webp"
                               class="mt-1 block w-full text-sm text-gray-500 
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-green-50 file:text-green-700
                                      hover:file:bg-green-100"/>
                        @error('alur_pendaftaran') 
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>
                    
                    {{-- Blok Konten Teks Informasi --}}
                    <div class="mb-4">
                        <label for="important_info_content" class="block text-base font-semibold text-gray-700 mb-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="brown" class="size-4 me-2">
                            <path d="M13.488 2.513a1.75 1.75 0 0 0-2.475 0L6.75 6.774a2.75 2.75 0 0 0-.596.892l-.848 2.047a.75.75 0 0 0 .98.98l2.047-.848a2.75 2.75 0 0 0 .892-.596l4.261-4.262a1.75 1.75 0 0 0 0-2.474Z" />
                            <path d="M4.75 3.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h6.5c.69 0 1.25-.56 1.25-1.25V9A.75.75 0 0 1 14 9v2.25A2.75 2.75 0 0 1 11.25 14h-6.5A2.75 2.75 0 0 1 2 11.25v-6.5A2.75 2.75 0 0 1 4.75 2H7a.75.75 0 0 1 0 1.5H4.75Z" />
                            </svg>
                            Isi Pengumuman / Informasi Untuk Peserta SPMB
                        </label>
                        
                        <textarea name="important_info_content" id="important_info_content" rows="15" 
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-inner p-3 focus:ring-blue-500 focus:border-blue-500 text-gray-800 tracking-wide resize-y">@if (old('important_info_content')){{ old('important_info_content') }}@else{{ $setting->important_info_content }}@endif</textarea>
                        
                        @error('important_info_content') 
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-150 transform hover:scale-[1.02]">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 inline me-1">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection