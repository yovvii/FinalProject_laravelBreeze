<div class="hidden lg:flex min-h-screen">
    <aside class="w-64 bg-blue-900 text-white flex flex-col fixed h-screen">
        <div>
            <img src="{{ asset('assets/logo_app_3.png') }}" 
                alt="Logo Aplikasi" 
                class="md:w-[50%] w-[50%] md:mt-5 md:mb-1 mb-0 mt-[30%] object-contain mx-auto" />
        </div>

        @php
            // Inisialisasi $data_sma jika belum terdefinisi
            $data_sma = $data_sma ?? null; 

            // 🔥 Adaptasi Logika: Cek apakah $data_sma ada dan punya logo
            $logoSource = ($data_sma && $data_sma->logo_sma) 
                ? asset('storage/' . $data_sma->logo_sma) 
                : asset('assets/profile_sekolah_jpg/avatar_empty.jpg'); // Asumsi: Anda memiliki placeholder ini
        @endphp

        <div class="w-full grid place-items-center">
            <div class="relative w-[70px] h-[70px] flex-shrink-0 bg-white rounded-full p-1"> 
                <img src="{{ $logoSource }}" 
                    alt="Logo {{ $data_sma->nama_sma ?? 'SMA' }}"
                    class="rounded-full w-full h-full object-cover"> 
            </div>

            <span class="mt-2 font-black">{{ Auth::user()->name }}</span>
            {{-- <span class="font-light text-sm tracking-wide">{{ Auth::user()->siswa->nisn }}</span> --}}
        </div>

        <nav class="flex-1 mt-6 px-5">
            <ul class="space-y-4">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-lg
                    {{ request()->routeIs('admin.dashboard') ? 'bg-blue-500' : 'bg-blue-900 hover:bg-blue-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6">
                        <path d="M8.543 2.232a.75.75 0 0 0-1.085 0l-5.25 5.5A.75.75 0 0 0 2.75 9H4v4a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1a1 1 0 1 1 2 0v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V9h1.25a.75.75 0 0 0 .543-1.268l-5.25-5.5Z" />
                        </svg>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.jalur_pendaftaran.index') }}" class="flex items-center px-3 py-2 rounded-lg
                    {{ request()->routeIs('admin.jalur_pendaftaran.index') || request()->routeIs('admin.jalur_pendaftaran.show') || request()->routeIs('siswa.detail') ? 'bg-blue-500' : 'bg-blue-900 hover:bg-blue-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6">
                            <path fill-rule="evenodd" d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V3Zm9 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm-6.25-.75a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5ZM11.5 6A.75.75 0 1 1 13 6a.75.75 0 0 1-1.5 0Z" clip-rule="evenodd" />
                            <path d="M13 11.75a.75.75 0 0 0-1.5 0v.179c0 .15-.138.28-.306.255A65.277 65.277 0 0 0 1.75 11.5a.75.75 0 0 0 0 1.5c3.135 0 6.215.228 9.227.668A1.764 1.764 0 0 0 13 11.928v-.178Z" />
                        </svg>
                        <span class="ml-3">Cek Validasi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.show_peringkat_murid') }}" class="flex items-center px-3 py-2 rounded-lg
                    {{ request()->routeIs('admin.show_peringkat_murid') || request()->routeIs('admin.peringkat_murid.show') ? 'bg-blue-500' : 'bg-blue-900 hover:bg-blue-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6">
                            <path fill-rule="evenodd" d="M2 2.75A.75.75 0 0 1 2.75 2h9.5a.75.75 0 0 1 0 1.5h-9.5A.75.75 0 0 1 2 2.75ZM2 6.25a.75.75 0 0 1 .75-.75h5.5a.75.75 0 0 1 0 1.5h-5.5A.75.75 0 0 1 2 6.25Zm0 3.5A.75.75 0 0 1 2.75 9h3.5a.75.75 0 0 1 0 1.5h-3.5A.75.75 0 0 1 2 9.75ZM9.22 9.53a.75.75 0 0 1 0-1.06l2.25-2.25a.75.75 0 0 1 1.06 0l2.25 2.25a.75.75 0 0 1-1.06 1.06l-.97-.97v5.69a.75.75 0 0 1-1.5 0V8.56l-.97.97a.75.75 0 0 1-1.06 0Z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-3">Peringkat Murid</span>
                    </a>
                </li>
                {{-- <li>
                    <a href="{{ route('test_field') }}" class="flex items-center px-3 py-2 rounded-lg
                    {{ request()->routeIs('test_field') || request()->routeIs('test_field') ? 'bg-blue-500' : 'bg-blue-900 hover:bg-blue-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path fill-rule="evenodd" d="M10.5 3.798v5.02a3 3 0 0 1-.879 2.121l-2.377 2.377a9.845 9.845 0 0 1 5.091 1.013 8.315 8.315 0 0 0 5.713.636l.285-.071-3.954-3.955a3 3 0 0 1-.879-2.121v-5.02a23.614 23.614 0 0 0-3 0Zm4.5.138a.75.75 0 0 0 .093-1.495A24.837 24.837 0 0 0 12 2.25a25.048 25.048 0 0 0-3.093.191A.75.75 0 0 0 9 3.936v4.882a1.5 1.5 0 0 1-.44 1.06l-6.293 6.294c-1.62 1.621-.903 4.475 1.471 4.88 2.686.46 5.447.698 8.262.698 2.816 0 5.576-.239 8.262-.697 2.373-.406 3.092-3.26 1.47-4.881L15.44 9.879A1.5 1.5 0 0 1 15 8.818V3.936Z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-3">Test Field</span>
                    </a>
                </li> --}}
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-3 py-2 rounded-lg hover:bg-red-600 bg-red-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-4.28 9.22a.75.75 0 0 0 0 1.06l3 3a.75.75 0 1 0 1.06-1.06l-1.72-1.72h5.69a.75.75 0 0 0 0-1.5h-5.69l1.72-1.72a.75.75 0 0 0-1.06-1.06l-3 3Z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-3">Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="flex-1 overflow-y-auto">
        </div>
</div>