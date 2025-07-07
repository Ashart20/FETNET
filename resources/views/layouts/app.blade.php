<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class=""> {{-- Class 'dark' akan ditambahkan di sini --}}
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} | Sistem Penjadwalan Otomatis FPTK UPI</title>

    {{-- Skrip PENTING: harus di <head> untuk mencegah layar putih berkedip (FOUC) saat mode gelap aktif --}}
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100 dark:bg-dark-primary">

    {{-- =================================================================== --}}
    {{-- AWAL DARI NAVBAR YANG TELAH DIPERBAIKI SECARA TOTAL --}}
    {{-- =================================================================== --}}
    <nav class="bg-white dark:bg-dark-secondary shadow-sm border-b border-gray-200 dark:border-dark-tertiary">
        {{-- PERBAIKAN: Padding horizontal (px) ditambah dan padding vertikal (py) ditambahkan untuk tinggi --}}
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">
            <div class="flex items-center justify-between py-4"> {{-- PERBAIKAN: Menggunakan padding vertikal 'py-4' dan menghilangkan 'h-16' --}}

                {{-- Bagian Kiri: Logo & Link Navigasi --}}
                <div class="flex items-center space-x-10">
                    {{-- 1. Logo Aplikasi --}}
                    <a href="{{ auth()->check() ? (auth()->user()->hasRole('fakultas') ? route('fakultas.dashboard') : (auth()->user()->hasRole('prodi') ? route('prodi.dashboard') : route('mahasiswa.dashboard'))) : route('home') }}" class="flex-shrink-0 flex items-center">
                        <img src="{{ asset('logo-fetnet-modern.png') }}" alt="Logo Fetnet" class="h-10 w-auto">
                        <span class="ml-4 text-2xl font-bold text-gray-800 dark:text-white">FETNET</span>
                    </a>

                    {{-- 2. Link Navigasi --}}
                    <div class="hidden md:flex items-center space-x-8">
                        {{-- PERBAIKAN: Ukuran font dan ikon diperbesar, gaya link aktif dibuat lebih menonjol --}}
                        <div class="hidden md:flex items-center space-x-8">
                        {{-- Link Dashboard (Sekarang dinamis) --}}
                            @auth
                        @role('fakultas')
                        <a href="{{ route('fakultas.dashboard') }}" class="flex items-center transition-colors text-base {{ request()->routeIs('fakultas.dashboard') ? 'text-indigo-600 dark:text-white font-semibold' : 'text-gray-500 dark:text-text-secondary hover:text-gray-900 dark:hover:text-white' }}">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2 2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Dashboard
                        </a>
                        @endrole
                        @role('prodi')
                        <a href="{{ route('prodi.dashboard') }}" class="flex items-center transition-colors text-base {{ request()->routeIs('prodi.dashboard') ? 'text-indigo-600 dark:text-white font-semibold' : 'text-gray-500 dark:text-text-secondary hover:text-gray-900 dark:hover:text-white' }}">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2 2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Dashboard
                        </a>
                        @endrole
                        @role('mahasiswa')
                        <a href="{{ route('mahasiswa.dashboard') }}" class="flex items-center transition-colors text-base {{ request()->routeIs('mahasiswa.dashboard') ? 'text-indigo-600 dark:text-white font-semibold' : 'text-gray-500 dark:text-text-secondary hover:text-gray-900 dark:hover:text-white' }}">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2 2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Dashboard
                        </a>
                        @endrole
                                {{-- Menu KHUSUS untuk peran FAKULTAS dengan Dropdown --}}
                                @role('fakultas')

                                {{-- Dropdown untuk Manajemen Prodi & User --}}
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="flex items-center transition-colors text-base text-gray-500 dark:text-text-secondary hover:text-gray-900 dark:hover:text-white whitespace-nowrap">
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Prodi & User
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    {{-- Panel Dropdown --}}
                                    <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-2 w-56 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                                        <div class="py-1">
                                            <a href="{{ route('fakultas.prodi') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Manajemen Prodi</a>
                                            <a href="{{ route('fakultas.users') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Manajemen User Prodi</a>
                                        </div>
                                    </div>
                                </div>

                                {{-- Dropdown untuk Manajemen Ruangan & Batasan --}}
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="flex items-center transition-colors text-base text-gray-500 dark:text-text-secondary hover:text-gray-900 dark:hover:text-white whitespace-nowrap">
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        Ruangan & Batasan
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    {{-- Panel Dropdown --}}
                                    <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-2 w-56 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                                        <div class="py-1">

                                            <a href="{{ route('fakultas.rooms') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Manajemen Ruangan</a>
                                            <a href="{{ route('fakultas.room-constraints') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Batasan Waktu Ruangan</a>
                                        </div>
                                    </div>
                                </div>

                                @endrole


                            {{-- Menu KHUSUS untuk peran PRODI dengan Dropdown --}}
                            @role('prodi')

                            {{-- Dropdown untuk Manajemen Data Master --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="flex items-center transition-colors text-base text-gray-500 dark:text-text-secondary hover:text-gray-900 dark:hover:text-white whitespace-nowrap">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                    Manajemen Data
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                {{-- Panel Dropdown --}}
                                <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-2 w-56 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                                    <div class="py-1">
                                        <a href="{{ route('prodi.teachers') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Manajemen Dosen</a>
                                        <a href="{{ route('prodi.subjects') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Manajemen Matkul</a>
                                        <a href="{{ route('prodi.student-groups') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Kelompok Mahasiswa</a>
                                        <a href="{{ route('prodi.activities') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Manajemen Aktivitas</a>
                                    </div>
                                </div>
                            </div>

                            {{-- Dropdown untuk Manajemen Batasan --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="flex items-center transition-colors text-base text-gray-500 dark:text-text-secondary hover:text-gray-900 dark:hover:text-white whitespace-nowrap">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Manajemen Batasan
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                {{-- Panel Dropdown --}}
                                <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-2 w-56 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                                    <div class="py-1">
                                        <a href="{{ route('prodi.teacher-constraints') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Batasan Waktu Dosen</a>
                                        <a href="{{ route('prodi.student-group-constraints') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Batasan Waktu Mahasiswa</a>
                                    </div>
                                </div>
                            </div>

                            @endrole
                        <a href="{{ route('hasil.fet') }}" class="flex items-center transition-colors text-base {{ request()->routeIs('hasil.fet') ? 'text-indigo-600 dark:text-white font-semibold' : 'text-gray-500 dark:text-text-secondary hover:text-gray-900 dark:hover:text-white '  }}">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Jadwal Utama
                        </a>
                        <a href="{{ route('user.index') }}" class="flex items-center transition-colors text-base {{ request()->routeIs('user.index') ? 'text-indigo-600 dark:text-white font-semibold' : 'text-gray-500 dark:text-text-secondary hover:text-gray-900 dark:hover:text-white' }}">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Pengguna
                        </a>
                    </div>
                </div>
                    @endauth
                {{-- Bagian Kanan: Tombol Toggle & Logout --}}
                <div class="flex items-center space-x-5">
                    {{-- 3. Tombol Toggle Dark Mode --}}
                    <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-tertiary focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg p-2.5">
                        {{-- PERBAIKAN: Ukuran ikon diperbesar menjadi w-6 h-6 --}}
                        <svg id="theme-toggle-dark-icon" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    </button>

                    {{-- 4. Tombol Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:inline">
                        @csrf
                        <button type="submit" class="flex items-center transition-colors text-base text-gray-500 dark:text-text-secondary hover:text-gray-900 dark:hover:text-white font-medium focus:outline-none">
                            {{-- PERBAIKAN: Ukuran ikon diperbesar menjadi h-5 w-5 --}}
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            {{-- PERBAIKAN: Menampilkan kembali teks Logout di layar besar --}}
                            <span class="hidden lg:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    {{-- =================================================================== --}}
    {{-- AKHIR DARI NAVBAR YANG TELAH DIPERBAIKI SECARA TOTAL --}}
    {{-- =================================================================== --}}

    <main>
        @if (isset($slot))
            {{-- Jika ini adalah halaman Livewire, variabel $slot akan ada --}}
            {{ $slot }}
        @else
            {{-- Jika ini adalah halaman Blade biasa, gunakan @yield --}}
            @yield('content')
        @endif
    </main>
</div>

{{-- Skrip untuk fungsionalitas tombol, diletakkan sebelum body berakhir --}}
<script>
    var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

    // Fungsi untuk mengatur ikon mana yang ditampilkan
    function setIconVisibility() {
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
            themeToggleDarkIcon.classList.add('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
            themeToggleLightIcon.classList.add('hidden');
        }
    }

    // Jalankan saat halaman dimuat
    setIconVisibility();

    var themeToggleBtn = document.getElementById('theme-toggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            // Toggle tema
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
            // Perbarui ikon setelah toggle
            setIconVisibility();
        });
    }
</script>
@livewireScripts
</body>
</html>
