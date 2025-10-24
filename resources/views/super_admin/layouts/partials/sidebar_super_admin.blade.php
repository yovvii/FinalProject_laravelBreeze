<div class="hidden lg:flex min-h-screen">
    <aside class="w-64 bg-blue-900 text-white flex flex-col fixed h-screen">
        <div>
            <img src="{{ asset('assets/logo_app_3.png') }}" 
                alt="Logo Aplikasi" 
                class="md:w-[50%] w-[50%] md:mt-5 md:mb-1 mb-0 mt-[30%] object-contain mx-auto" />
        </div>

        <div class="w-full grid place-items-center">
            <span class="font-black italic">{{ Auth::user()->name }}</span>
            {{-- <span class="font-light text-sm tracking-wide">{{ Auth::user()->siswa->nisn }}</span> --}}
        </div>

        <nav class="flex-1 mt-6 px-5">
            <ul class="space-y-4">
                <li>
                    <a href="{{ route('super_admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-lg
                    {{ request()->routeIs('super_admin.dashboard') ? 'bg-blue-500' : 'bg-blue-900 hover:bg-blue-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6">
                        <path d="M10.536 3.444a.75.75 0 0 0-.571-1.387L3.5 4.719V3.75a.75.75 0 0 0-1.5 0v1.586l-.535.22A.75.75 0 0 0 2 6.958V12.5h-.25a.75.75 0 0 0 0 1.5H4a1 1 0 0 0 1-1v-1a1 1 0 1 1 2 0v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V3.664l.536-.22ZM11.829 5.802a.75.75 0 0 0-.333.623V8.5c0 .027.001.053.004.08V13a1 1 0 0 0 1 1h.5a1 1 0 0 0 1-1V7.957a.75.75 0 0 0 .535-1.4l-2.004-.826a.75.75 0 0 0-.703.07Z" />
                        </svg>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('super_admin.data_sma') }}" class="flex items-center px-3 py-2 rounded-lg
                    {{ request()->routeIs('super_admin.data_sma') || request()->routeIs('super_admin.sma.create') || request()->routeIs('super_admin.sma.edit') ? 'bg-blue-500' : 'bg-blue-900 hover:bg-blue-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M7.605 2.112a.75.75 0 0 1 .79 0l5.25 3.25A.75.75 0 0 1 13 6.707V12.5h.25a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1 0-1.5H3V6.707a.75.75 0 0 1-.645-1.345l5.25-3.25ZM4.5 8.75a.75.75 0 0 1 1.5 0v3a.75.75 0 0 1-1.5 0v-3ZM8 8a.75.75 0 0 0-.75.75v3a.75.75 0 0 0 1.5 0v-3A.75.75 0 0 0 8 8Zm2 .75a.75.75 0 0 1 1.5 0v3a.75.75 0 0 1-1.5 0v-3ZM8 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-3">Data Sekolah</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('super_admin.data_admin_sekolah') }}" class="flex items-center px-3 py-2 rounded-lg
                    {{ request()->routeIs('super_admin.data_admin_sekolah') || request()->routeIs('test_field') ? 'bg-blue-500' : 'bg-blue-900 hover:bg-blue-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-3">Admin Sekolah</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('super_admin.data_diterima') }}" class="flex items-center px-3 py-2 rounded-lg
                    {{ request()->routeIs('super_admin.data_diterima') ? 'bg-blue-500' : 'bg-blue-900 hover:bg-blue-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M8 1.75a.75.75 0 0 1 .692.462l1.41 3.393 3.664.293a.75.75 0 0 1 .428 1.317l-2.791 2.39.853 3.575a.75.75 0 0 1-1.12.814L7.998 12.08l-3.135 1.915a.75.75 0 0 1-1.12-.814l.852-3.574-2.79-2.39a.75.75 0 0 1 .427-1.318l3.663-.293 1.41-3.393A.75.75 0 0 1 8 1.75Z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-3">Data Murid Diterima</span>
                    </a>
                </li>
                {{-- Dropdown: Pengaturan Global --}}
                <li x-data="{ open: {{ request()->routeIs('banner.index') || request()->routeIs('usia.siswa.index') || request()->routeIs('super_admin.informasi.index') || request()->routeIs('super_admin.stop.index') ? 'true' : 'false' }} }" class="mb-2">

                    {{-- Tombol utama dropdown --}}
                    <button 
                        @click="open = !open" 
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg bg-blue-900 hover:bg-blue-600 text-white font-medium transition-all">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <span>Pengaturan Global</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" 
                            :class="{ 'rotate-180': open }" 
                            class="w-5 h-5 transform transition-transform duration-200" 
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Isi dropdown --}}
                    <ul x-show="open" 
                        x-transition 
                        class="mt-2 pl-4 space-y-1 overflow-hidden"
                        style="display: none;">

                        {{-- Banner Informasi --}}
                        <li>
                            <a href="{{ route('banner.index') }}" 
                            class="flex items-center px-3 py-2 rounded-lg transition 
                            {{ request()->routeIs('banner.index') ? 'bg-blue-500 text-white' : 'bg-blue-900 text-gray-100 hover:bg-blue-700' }}">
                                {{-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                    <path d="M5.75 7.5a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5ZM7.25 8.25A.75.75 0 0 1 8 7.5h2.25a.75.75 0 0 1 0 1.5H8a.75.75 0 0 1-.75-.75ZM5.75 9.5a.75.75 0 0 0 0 1.5H8a.75.75 0 0 0 0-1.5H5.75Z" />
                                    <path fill-rule="evenodd" d="M4.75 1a.75.75 0 0 0-.75.75V3a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2V1.75a.75.75 0 0 0-1.5 0V3h-5V1.75A.75.75 0 0 0 4.75 1ZM3.5 7a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v4.5a1 1 0 0 1-1 1h-7a1 1 0 0 1-1-1V7Z" clip-rule="evenodd" />
                                </svg> --}}
                                <span class="ml-5">Banner Informasi</span>
                            </a>
                        </li>

                        {{-- Pengaturan Usia --}}
                        <li>
                            <a href="{{ route('usia.siswa.index') }}" 
                            class="flex items-center px-3 py-2 rounded-lg transition 
                            {{ request()->routeIs('usia.siswa.index') ? 'bg-blue-500 text-white' : 'bg-blue-900 text-gray-100 hover:bg-blue-700' }}">
                                {{-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                    <path fill-rule="evenodd" d="M1 8a7 7 0 1 1 14 0A7 7 0 0 1 1 8Zm7.75-4.25a.75.75 0 0 0-1.5 0V8c0 .414.336.75.75.75h3.25a.75.75 0 0 0 0-1.5h-2.5v-3.5Z" clip-rule="evenodd" />
                                </svg> --}}
                                <span class="ml-5">Batas Usia</span>
                            </a>
                        </li>

                        {{-- Pengaturan Informasi --}}
                        <li>
                            <a href="{{ route('super_admin.informasi.index') }}" 
                            class="flex items-center px-3 py-2 rounded-lg transition 
                            {{ request()->routeIs('super_admin.informasi.index') ? 'bg-blue-500 text-white' : 'bg-blue-900 text-gray-100 hover:bg-blue-700' }}">
                                {{-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v9a2 2 0 0 0 2 2h8a2 2 0 0 1-2-2V3ZM4 4h4v2H4V4Zm4 3.5H4V9h4V7.5Zm-4 3h4V12H4v-1.5Z" clip-rule="evenodd" />
                                    <path d="M13 5h-1.5v6.25a1.25 1.25 0 1 0 2.5 0V6a1 1 0 0 0-1-1Z" />
                                </svg> --}}
                                <span class="ml-5">Informasi Umum</span>
                            </a>
                        </li>
                        
                        {{-- Pengaturan Status Spmb --}}
                        <li>
                            <a href="{{ route('super_admin.stop.index') }}" 
                            class="flex items-center px-3 py-2 rounded-lg transition 
                            {{ request()->routeIs('super_admin.stop.index') ? 'bg-blue-500 text-white' : 'bg-blue-900 text-gray-100 hover:bg-blue-700' }}">
                                {{-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v9a2 2 0 0 0 2 2h8a2 2 0 0 1-2-2V3ZM4 4h4v2H4V4Zm4 3.5H4V9h4V7.5Zm-4 3h4V12H4v-1.5Z" clip-rule="evenodd" />
                                    <path d="M13 5h-1.5v6.25a1.25 1.25 0 1 0 2.5 0V6a1 1 0 0 0-1-1Z" />
                                </svg> --}}
                                <span class="ml-5">Status SPMB</span>
                            </a>
                        </li>
                    </ul>
                </li>
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