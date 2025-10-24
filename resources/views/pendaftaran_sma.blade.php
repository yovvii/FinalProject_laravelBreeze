<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pendaftaran SMA') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-4 lg:px-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="bg-blue-600 py-2 rounded-t-xl">
                    <p class="text-white text-center font-poppins text-md font-semibold">Daftar SMA Kabupaten Purbalingga</p>
                </div>
                <div class="border border-gray-400 p-6 rounded-b-lg rounded-t-xl space-y-[30px] md:space-y-[50px] m-4">
                    @foreach ($data_sekolah_sma as $data_sma)
                        <div class="flex justify-between border-b-2 border-gray-100">
                            <div class="flex my-auto">
                                <div class="w-[25px] md:w-[50px] max-h-[50px] me-3 my-auto">
                                    @if ($data_sma->logo_sma)
                                        <img class="w-full h-full object-cover" src="{{ asset('storage/' . $data_sma->logo_sma) }}" alt="Logo {{ $data_sma->nama_sma }}">
                                    @else
                                        {{-- Placeholder jika tidak ada logo --}}
                                        <div class="w-full h-full bg-gray-200 rounded-full flex items-center justify-center">
                                            <span class="text-xs text-gray-500">Logo</span>
                                        </div>
                                    @endif
                                </div>
                                <p class="text-[12px]/[18px] font-bold my-auto md:text-[16px]/[20px]">{{ $data_sma->nama_sma }}<br>
                                    @if ($data_sma->akreditasi)
                                    <span class="{{ $data_sma->akreditasi->warna_background }} rounded-full px-4 py-[1px] {{ $data_sma->akreditasi->warna_text }} text-[8px] md:text-[10px] font-thin">Akreditasi {{ $data_sma->akreditasi->jenis_akreditasi }}</span>
                                    @else
                                    <span class="bg-gray-200 rounded-full px-4 py-[1px] text-gray-500 text-[10px] font-thin">Tidak Terakreditasi</span>
                                    @endif
                                </p>
                            </div>
                            <div class="flex my-auto">
                                <p class="me-5 text-[16px] my-auto hidden md:block">
                                    <span class="font-bold">{{ number_format($data_sma->siswas_count, 0, ',', '.') }}</span> Pendaftar
                                </p>
                                <form action="{{ route('jalur_pendaftaran') }}" method="GET">
                                    @csrf
                                    <input type="hidden" name="sma_id" value="{{ $data_sma->id }}">
                                    <button type="submit" class="bg-blue-600 rounded-lg px-[15px] py-[1px] text-white font-bold text-sm">
                                        DAFTAR
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</x-app-layout>