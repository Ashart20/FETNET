<x-guest-layout>
    {{-- Tambahkan Slot untuk Logo dan Judul --}}
    <div class="mb-4">
        {{-- Logo Fetnet --}}
        <a href="/">
            <img src="{{ asset('logo-fetnet-modern.png') }}" alt="Logo Fetnet" class="h-24 w-auto mx-auto object-contain">
        </a>
        <h2 class="mt-4 text-center text-3xl font-extrabold text-gray-900">
            Daftar ke <span class="text-indigo-600">FETNET</span>
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Buat akun untuk akses penuh ke Fetnet
        </p>
    </div>

    <x-auth-session-status class="mb-4 text-green-600" :status="session('status')" /> {{-- Session status juga di register --}}

    {{-- Input Error secara manual (seperti di login) --}}
    @if ($errors->any())
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200" role="alert">
            <div class="font-medium text-red-800">
                {{ __('Whoops! Ada yang salah.') }}
            </div>

            <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="text-gray-700" />
            <x-text-input id="name" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-gray-700" />
            <x-text-input id="password" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                          type="password"
                          name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-gray-700" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature()) {{-- Ini mungkin tidak ada jika Anda pakai Breeze saja --}}
        <div class="mt-4">
            <x-input-label for="terms">
                <div class="flex items-center">
                    <input id="terms" type="checkbox" name="terms" required class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />

                    <div class="ms-2 text-sm text-gray-600">
                        {!! __('Saya setuju dengan :terms_of_service dan :privacy_policy', [
                                'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-indigo-600 hover:text-indigo-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Ketentuan Layanan').'</a>',
                                'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-indigo-600 hover:text-indigo-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Kebijakan Privasi').'</a>',
                        ]) !!}
                    </div>
                </div>
            </x-input-label>
        </div>
        @endif

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-indigo-600 hover:text-indigo-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Sudah terdaftar?') }}
            </a>

            <x-primary-button class="ms-4 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
