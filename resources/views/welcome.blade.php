<x-app-layout>
    {{-- Konten di bawah ini akan otomatis dimasukkan ke dalam {{ $slot }} di app.blade.php --}}

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- PERBAIKAN: Card akan mengatur warna teks secara otomatis --}}
            <x-mary-card class="text-center">

                {{-- Konten utama di dalam Card --}}
                {{-- PERBAIKAN: Menghapus kelas warna `text-gray-900 dark:text-white` --}}
                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">
                    Selamat Datang di FETNET
                </h1>

                {{-- PERBAIKAN: Menghapus kelas warna `text-gray-600 dark:text-gray-400` --}}
                <p class="mt-4 text-lg max-w-3xl mx-auto">
                    Sistem Informasi Penjadwalan Otomatis FPTI Universitas Pendidikan Indonesia.
                </p>
                <p class="mt-2 text-lg max-w-3xl mx-auto">
                    Platform kolaboratif untuk merancang, mengatur, dan mengotomatiskan proses penjadwalan kompleks berbasis algoritma FET.
                </p>

                {{-- Slot 'actions' untuk tombol-tombol --}}
                <x-slot:actions>
                    @guest
                        <x-mary-button label="Masuk" link="{{ route('login') }}" class="btn-primary" />
                        @if (Route::has('register'))
                            <x-mary-button label="Daftar" link="{{ route('register') }}" class="btn-secondary" />
                        @endif
                    @endguest

                    @auth
                        @php
                            $dashboardRoute = match (true) {
                                auth()->user()->hasRole('fakultas') => route('fakultas.dashboard'),
                                auth()->user()->hasRole('prodi') => route('prodi.dashboard'),
                                default => route('mahasiswa.dashboard'),
                            };
                        @endphp
                        <x-mary-button label="Buka Dashboard" :link="$dashboardRoute" class="btn-primary" />
                    @endauth
                </x-slot:actions>

            </x-mary-card>
        </div>
    </div>

</x-app-layout>
