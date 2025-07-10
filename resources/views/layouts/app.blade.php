{{-- ashart20/fetnet/FETNET-f0e9b4000a74552ad1e6adafe3c041322f28764b/resources/views/layouts/app.blade.php --}}

    <!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FETNET - Sistem Penjadwalan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen font-sans antialiased bg-base-200">

{{-- Workaround: Navbar manual menggunakan kelas DaisyUI --}}
<div class="navbar bg-base-100 shadow-md sticky top-0 z-30 px-4">
    <div class="navbar-start">
        {{-- Logo --}}
        <a href="/" wire:navigate class="btn btn-ghost text-xl">
            <img src="{{ asset('logo-fetnet-modern.png') }}" alt="Logo Fetnet" class="h-8 w-auto">
            <span class="ml-2 hidden sm:inline">FETNET</span>
        </a>
    </div>
    <div class="navbar-center hidden lg:flex">
        {{-- Menu Manual dengan komponen Mary UI --}}
        <ul class="menu menu-horizontal px-1">
            @auth
                {{-- LINK UMUM - DASHBOARD DINAMIS --}}
                <li>
                    @php
                        $dashboardRouteName = '';
                        if (auth()->user()->hasRole('fakultas')) {
                            $dashboardRouteName = 'fakultas.dashboard';
                        } elseif (auth()->user()->hasRole('prodi')) {
                            $dashboardRouteName = 'prodi.dashboard';
                        } elseif (auth()->user()->hasRole('mahasiswa')) {
                            $dashboardRouteName = 'mahasiswa.dashboard';
                        }
                    @endphp

                    @if ($dashboardRouteName)
                        <a href="{{ route($dashboardRouteName) }}" @if(request()->routeIs($dashboardRouteName)) class="active" @endif>
                            <x-mary-icon name="o-home" /> Dashboard
                        </a>
                    @else
                        {{-- Fallback jika user tidak memiliki peran dashboard yang terdefinisi --}}
                        <a href="/dashboard" class="btn btn-ghost">
                            <x-mary-icon name="o-home" /> Dashboard (Default)
                        </a>
                    @endif
                </li>

                {{-- MENU FAKULTAS --}}
                @role('fakultas')
                <li>
                    <details>
                        <summary>
                            <x-mary-icon name="o-academic-cap" /> Prodi & User
                        </summary>
                        <ul class="p-2 bg-base-100 rounded-t-none z-20">
                            <li><a href="{{ route('fakultas.prodi') }}" @if(request()->routeIs('fakultas.prodi')) class="active" @endif>Manajemen Prodi</a></li>
                        </ul>
                    </details>
                </li>
                <li>
                    <details>
                        <summary>
                            <x-mary-icon name="o-building-office" /> Ruangan & Batasan
                        </summary>
                        <ul class="p-2 bg-base-100 rounded-t-none z-20">
                            <li><a href="{{ route('fakultas.rooms') }}" @if(request()->routeIs('fakultas.rooms')) class="active" @endif>Manajemen Ruangan</a></li>
                            <li><a href="{{ route('fakultas.preferred-rooms') }}" @if(request()->routeIs('fakultas.preferred-rooms')) class="active" @endif>Preferensi Ruangan Aktivitas</a></li>
                            <li><a href="{{ route('fakultas.room-constraints') }}" @if(request()->routeIs('fakultas.room-constraints')) class="active" @endif>Batasan Waktu Ruangan</a></li>
                        </ul>
                    </details>
                </li>
                @endrole

                {{-- MENU PRODI --}}
                @role('prodi')
                <li>
                    <details>
                        <summary>
                            <x-mary-icon name="o-archive-box" /> Manajemen Data
                        </summary>
                        <ul class="p-2 bg-base-100 rounded-t-none z-20">
                            <li><a href="{{ route('prodi.teachers') }}" @if(request()->routeIs('prodi.teachers')) class="active" @endif>Dosen</a></li>
                            <li><a href="{{ route('prodi.subjects') }}" @if(request()->routeIs('prodi.subjects')) class="active" @endif>Mata Kuliah</a></li>
                            <li><a href="{{ route('prodi.student-groups') }}" @if(request()->routeIs('prodi.student-groups')) class="active" @endif>Kelompok Mahasiswa</a></li>
                            <li><a href="{{ route('prodi.activities') }}" @if(request()->routeIs('prodi.activities')) class="active" @endif>Aktivitas</a></li>
                        </ul>
                    </details>
                </li>
                <li>
                    <details>
                        <summary>
                            <x-mary-icon name="o-clock" /> Manajemen Batasan
                        </summary>
                        <ul class="p-2 bg-base-100 rounded-t-none z-20">
                            <li><a href="{{ route('prodi.teacher-constraints') }}" @if(request()->routeIs('prodi.teacher-constraints')) class="active" @endif>Batasan Dosen</a></li>
                            <li><a href="{{ route('prodi.student-group-constraints') }}" @if(request()->routeIs('prodi.student-group-constraints')) class="active" @endif>Batasan Mahasiswa</a></li>
                        </ul>
                    </details>
                </li>
                @endrole

                {{-- LINK JADWAL UNTUK FAKULTAS --}}
                @role('fakultas')
                <li>
                    <a href="{{ route('fakultas.schedules.index') }}" @if(request()->routeIs('fakultas.schedules.index')) class="active" @endif>
                        <x-mary-icon name="o-calendar-days" />
                        Jadwal Utama
                    </a>
                </li>
                <li>
                    <a href="{{ route('fakultas.generate.index') }}" @if(request()->routeIs('fakultas.generate.index')) class="active" @endif>
                        <x-mary-icon name="o-rocket-launch" />
                        Generate Jadwal
                    </a>
                </li>
                @endrole

                @role('prodi')
                <li>
                    <a href="{{ route('hasil.fet') }}" @if(request()->routeIs('hasil.fet')) class="active" @endif>
                        <x-mary-icon name="o-calendar-days" />
                        Jadwal Utama
                    </a>
                </li>
                @endrole
            @endauth
        </ul>
    </div>
    <div class="navbar-end">
        {{-- Aksi di Kanan --}}
        <x-mary-theme-toggle class="btn btn-ghost btn-circle" />

        @auth

            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost">
                    {{-- Tampilkan nama pengguna yang sedang login --}}
                    <span>{{ Auth::user()->name }}</span>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[20] p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a href="{{ route('profile.edit') }}">Profil</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" >
                            @csrf
                            <button type="submit" class="w-full text-left">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        @endauth
    </div>
</div>

{{-- Konten Utama Halaman --}}
<main class="p-4 sm:p-6 lg:p-8">
    {{ $slot }}
</main>

{{-- Toast Notifikasi Global --}}
<x-mary-toast />
@livewireScripts
</body>
</html>
