<x-app-layout>
    {{-- Kita menggunakan layout app.blade.php sebagai dasarnya --}}
    {{-- Konten di bawah ini akan otomatis dimasukkan ke dalam {{ $slot }} --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-10 md:p-20 text-center border-b border-gray-200 dark:border-gray-700">

                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white leading-tight">
                        Selamat Datang di FETNET
                    </h1>

                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                        Sistem Informasi Penjadwalan Otomatis FPTI Universitas Pendidikan Indonesia.
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                        Platform kolaboratif untuk merancang, mengatur, dan mengotomatiskan proses penjadwalan kompleks berbasis algoritma FET.
                    </p>
                    </p>

                    {{-- Tombol ini hanya akan tampil jika user belum login --}}
                    @guest
                        <div class="mt-8 flex justify-center gap-4">
                            <a href="{{ route('login') }}" class="inline-block rounded-lg bg-indigo-600 px-6 py-3 text-base font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Masuk
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-block rounded-lg bg-gray-200 dark:bg-gray-700 px-6 py-3 text-base font-medium text-gray-800 dark:text-gray-200 transition hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800">
                                    Daftar
                                </a>
                            @endif
                        </div>
                    @endguest

                    {{-- Tombol ini hanya akan tampil jika user sudah login --}}
                    @auth
                        <div class="mt-8">
                            <a href="{{ auth()->user()->hasRole('fakultas') ? route('fakultas.dashboard') : (auth()->user()->hasRole('prodi') ? route('prodi.dashboard') : route('mahasiswa.dashboard')) }}"
                               class="inline-block rounded-lg bg-indigo-600 px-6 py-3 text-base font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Buka Dashboard
                            </a>
                        </div>
                    @endauth

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
