<x-guest-layout>
<form method="POST" action="{{ route('login') }}">
    @csrf
    <!-- NISN -->
    <div class="">
        <label for="nisn" class="block text-sm font-medium text-gray-700">
            NISN
        </label>
        <input id="nisn" type="text" name="nisn" value="{{ old('nisn') }}" class="mt-1 block w-full rounded-full shadow-sm focus:border-indigo-500 font-poppins
                focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700 bg-blue-50 text-black"
            required/>

        @error('nisn')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div class="mt-5">
        <label for="password" class="block text-sm font-medium text-gray-700">
            Password
        </label>
        <input id="password" type="password" name="password" autocomplete="current-password" class="mt-1 block w-full rounded-full shadow-sm focus:border-indigo-500 font-poppins
                focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700 bg-blue-50 text-black"
            required/>

        @error('password')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror

    </div>
    <p class="text-gray-400 text-sm mt-2 dark:text-gray-500">
        Tanggal Lahir sebagai password default (DDMMYYYY)
    </p>


    <!-- Remember Me -->
    {{-- <div class="mt-4 flex items-center justify-between">
        <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" class="rounded-full dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Ingat Saya') }}</span>
        </label>

        @if (Route::has('password.request'))
            <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                {{ __('Lupa Password?') }}
            </a>
        @endif
    </div> --}}
    
    <button class="mt-5 w-full bg-blue-900 py-2 rounded-xl font-poppins text-white">
        Log in
    </button>

    <p class="text-center my-2 text-xs">- <span class="text-gray-400">Belum punya akun?</span> -</p>
    
    <button 
    type="button"
    onclick="window.location='{{ route('register') }}'"
    class="w-full font-extrabold text-black bg-white border border-gray-500 rounded-xl py-2 text-center hover:bg-gray-100 transition">
    Registrasi
</button>
</form>
</x-guest-layout>