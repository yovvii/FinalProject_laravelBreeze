<x-guest-layout>
<form method="POST" action="{{ route('register') }}">
    @csrf
    <!-- NISN -->
    <div class="">
        <label for="nisn" class="block text-sm font-medium text-gray-700">
            NISN
        </label>
        <input id="nisn" name="nisn" type="text"
            value="{{ old('nisn') }}"
            required
            class="mt-1 block w-full rounded-full shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-blue-50 text-black"/>
        @error('nisn')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    </div>

    <!-- Nama -->
    <div class="mt-4">
        <label for="name" class="block text-sm font-medium text-gray-700">
            Nama
        </label>
        <input id="name" name="name" type="text"
            value="{{ old('name') }}"
            required
            autofocus
            autocomplete="name"
            class="mt-1 block w-full rounded-full shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-blue-50 text-black"/>
        @error('name')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    </div>

    <!-- Tanggal Lahir -->
    <div class="mt-4">
        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">
            Tanggal Lahir
        </label>
        <input id="tanggal_lahir" name="tanggal_lahir" type="date"
            value="{{ old('tanggal_lahir') }}"
            required
            class="mt-1 block w-full rounded-full shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-blue-50 text-black" />
        @error('tanggal_lahir')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    </div>

    <p class="text-gray-400 text-sm mt-2 dark:text-gray-500 text-center md:text-left">
        Pastikan input sesuai dengan data yang anda miliki
    </p>

    <button type="submit"
        class="w-full items-center bg-blue-900 px-4 py-2 rounded-xl text-white font-poppins mt-[8%]">
        Register
    </button>

    <p class="text-center my-2 text-xs">- <span class="text-gray-400">Sudah punya akun?</span> -</p>
    
    <button 
        type="button"
        onclick="window.location='{{ route('login') }}'"
        class="w-full font-extrabold text-black bg-white border border-gray-500 rounded-xl py-2 text-center hover:bg-gray-100 transition">
        Log In
    </button>

</form>
</x-guest-layout>