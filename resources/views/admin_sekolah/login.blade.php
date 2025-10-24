<x-guest-layout>
    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit"
                class="mt-5 w-full bg-blue-900 py-2 rounded-xl font-poppins text-white hover:bg-blue-800 transition duration-200">
                {{ __('Masuk Admin') }}
            </button>
        </div>

    </form>
</x-guest-layout>