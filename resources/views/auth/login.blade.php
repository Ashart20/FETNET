<x-guest-layout>
    {{-- Tambahkan Slot untuk Logo dan Judul --}}
    <div class="mb-4">
        {{-- Logo Fetnet --}}
        <a href="/">
            <img src="{{ asset('logo-fetnet-modern.png') }}" alt="Logo Fetnet" class="h-24 w-auto mx-auto object-contain">
        </a>
        <h2 class="mt-4 text-center text-3xl font-extrabold text-gray-900">
            Masuk ke <span class="text-indigo-600">FETNET</span>
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Sistem Informasi Penjadwalan Otomatis FPTK UPI
        </p>
    </div>

    <x-auth-session-status class="mb-4 text-green-600" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-gray-700" />
            <x-text-input id="password" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                          type="password"
                          name="password"
                          required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember"> {{-- Ganti class checkbox --}}
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat Saya') }}</span> {{-- Teks B. Indo --}}
            </label>
        </div>


        <div class="flex items-center justify-end mt-4">
            {{-- Teks B. Indo
            @if (Route::has('password.request'))
                <a class="underline text-sm text-indigo-600 hover:text-indigo-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Lupa Password?') }}
                </a>
            @endif
            --}}

            <x-primary-button class="ms-3 bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out"> {{-- Sesuaikan warna tombol --}}
                {{ __('Masuk') }} {{-- Teks B. Indo --}}
            </x-primary-button>
        </div>

        {{-- Tambahkan link untuk register
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">Belum punya akun? <a href="{{ route('register') }}" class="underline text-indigo-600 hover:text-indigo-800">Daftar Sekarang</a></p>
        </div>
        --}}
    </form>
</x-guest-layout>
