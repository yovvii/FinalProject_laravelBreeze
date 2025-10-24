<x-app-layout>
    <div class="py-5">
        <div class="max-w-full mx-auto px-4 sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="bg-blue-600 py-4">
                    <p class="text-white text-center font-poppins text-2xl font-semibold">Pemberitahuan</p>
                </div>
                
                <div class="p-5 bg-white flex flex-col space-y-4 ">
                    <div class="flex justify-between h-full items-center">
                        @if ($notifications->where('is_read', false)->count() > 0)
                            <form method="POST" action="{{ route('notification.mark_read') }}">
                                @csrf
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white rounded-md px-2 py-1 text-[10px] md:text-lg font-semibold transition duration-150 justify-center items-center">
                                    Tandai Semua Sudah Dibaca
                                </button>
                            </form>
                        @endif

                        {{-- Tombol Hapus Semua --}}
                        @if ($notifications->count() > 0)
                        <div class="flex justify-end ml-auto">
                            <form method="POST" action="{{ route('notification.clear_all') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua pemberitahuan? Aksi ini tidak dapat dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex gap-2 bg-red-500 hover:bg-red-600 text-white rounded-md px-2 py-1 text-[10px] md:text-lg font-semibold transition duration-150 justify-center items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-2 md:size-5">
                                    <path fill-rule="evenodd" d="M5 3.25V4H2.75a.75.75 0 0 0 0 1.5h.3l.815 8.15A1.5 1.5 0 0 0 5.357 15h5.285a1.5 1.5 0 0 0 1.493-1.35l.815-8.15h.3a.75.75 0 0 0 0-1.5H11v-.75A2.25 2.25 0 0 0 8.75 1h-1.5A2.25 2.25 0 0 0 5 3.25Zm2.25-.75a.75.75 0 0 0-.75.75V4h3v-.75a.75.75 0 0 0-.75-.75h-1.5ZM6.05 6a.75.75 0 0 1 .787.713l.275 5.5a.75.75 0 0 1-1.498.075l-.275-5.5A.75.75 0 0 1 6.05 6Zm3.9 0a.75.75 0 0 1 .712.787l-.275 5.5a.75.75 0 0 1-1.498-.075l.275-5.5a.75.75 0 0 1 .786-.711Z" clip-rule="evenodd" />
                                    </svg>
                                    Bersihkan Semua ({{ $notifications->count() }})
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>


                    {{-- Daftar Notifikasi --}}
                    @forelse ($notifications as $notification)
                        @php
                            // Tentukan warna berdasarkan tipe
                            $isSuccess = $notification->type === 'success';
                            // $bgColor = $isSuccess ? 'bg-green-400' : 'bg-red-400';
                            $iconBgColor = $isSuccess ? 'bg-green-300' : 'bg-red-300';
                            $iconBorderColor = $isSuccess ? 'border-green-700' : 'border-red-700';
                            $textColor = $isSuccess ? 'text-green-600' : 'text-red-600';
                            $statusText = $isSuccess ? 'Horeeee!' : 'Sayang sekali!';
                            $time = \Carbon\Carbon::parse($notification->created_at)->format('d M Y, H:i');
                            $isUnreadClass = $notification->is_read ? '' : ' border-2 border-yellow-300 shadow-lg';
                        @endphp

                        <div 
                            x-data="{ showFull: false }" 
                            x-on:click="showFull = !showFull"
                            class="bg-gray-100 py-2 px-4 rounded-lg flex items-center shadow-md cursor-pointer"
                        >
                            {{-- Icon Status --}}
                            <div class="{{ $iconBgColor }} min-w-[15px] h-[15px] rounded-full text-white flex items-center justify-center border {{ $iconBorderColor }}">
                                @if ($isSuccess)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10px" height="10px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check text-green-700"><polyline points="20 6 9 17 4 12"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10px" height="10px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x text-red-700"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                @endif
                            </div>
                            
                            <div class="ms-4 flex-grow">
                                {{-- Waktu --}}
                                <p class="text-blue-700 font-poppins text-[8px] md:text-[12px]">{{ $time }}</p> 
                                
                                {{-- Status --}}
                                <p class="{{ $textColor }} font-poppins font-semibold text-[13px] md:text-[18px]">{{ $statusText }}</p>
                                
                                {{-- Messages (dipotong / tampil full saat diklik) --}}
                                <p class="text-gray-800 font-poppins text-[12px] md:text-[16px]" x-text="showFull ? '{{ $notification->message }}' : '{{ Str::limit($notification->message, 30, '...') }}'"></p>
                            </div>
                            
                            {{-- Tombol Hapus --}}
                            <form method="POST" action="{{ route('notification.destroy', $notification) }}" class="ml-auto">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 rounded-full px-3 py-1 md:px-5 transition duration-150 hover:bg-gray-100">
                                    <p class="text-white font-poppins text-[8px] md:text-[16px]">Hapus</p>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-10 text-gray-500 font-poppins">
                            Belum ada pemberitahuan yang tersimpan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
