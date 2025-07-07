@extends('layouts.app')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8">

        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Selamat Datang, {{ Auth::user()->name ?? 'Pengguna' }}!</h1>
            <p class="text-gray-600 dark:text-text-secondary mt-1 text-lg">Ringkasan cepat dan statistik jadwal perkuliahan Anda.</p>
        </header>

        {{-- ======================================================= --}}
        {{-- BAGIAN KARTU STATISTIK UTAMA & SEKUNDER (GAYA TERPADU) --}}
        {{-- ======================================================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            {{-- KARTU TERPADU: Menggunakan style card yang sama untuk semua statistik --}}
            {{-- IKON MINIMALIS: Menghapus latar belakang solid pada ikon agar lebih elegan --}}

            {{-- Kartu: Total Jadwal --}}
            <div class="bg-white dark:bg-dark-secondary p-6 rounded-xl border border-gray-200 dark:border-dark-tertiary transition-all duration-300 hover:border-indigo-500 dark:hover:border-brand-purple">
                <div class="flex justify-between items-start">
                    <div class="flex flex-col">
                        <p class="text-sm font-medium text-gray-500 dark:text-text-secondary">Total Jadwal</p>
                        <span class="text-4xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalSchedules }}</span>
                    </div>
                    <svg class="h-7 w-7 text-indigo-500 dark:text-brand-purple" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18" /></svg>
                </div>
                <p class="text-sm text-green-500 dark:text-green-400 mt-4 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L12 11.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    +12% lebih baik
                </p>
            </div>

            {{-- Kartu: Jadwal Aktif --}}
            <div class="bg-white dark:bg-dark-secondary p-6 rounded-xl border border-gray-200 dark:border-dark-tertiary transition-all duration-300 hover:border-green-500">
                <div class="flex justify-between items-start">
                    <div class="flex flex-col">
                        <p class="text-sm font-medium text-gray-500 dark:text-text-secondary">Jadwal Aktif</p>
                        <span class="text-4xl font-bold text-gray-900 dark:text-white mt-1">{{ $activeSchedules }}</span>
                    </div>
                    <svg class="h-7 w-7 text-green-600 dark:text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <p class="text-sm text-green-500 dark:text-green-400 mt-4 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L12 11.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    +8% lebih banyak
                </p>
            </div>

            {{-- Kartu: Jadwal Nonaktif --}}
            <div class="bg-white dark:bg-dark-secondary p-6 rounded-xl border border-gray-200 dark:border-dark-tertiary transition-all duration-300 hover:border-red-500">
                <div class="flex justify-between items-start">
                    <div class="flex flex-col">
                        <p class="text-sm font-medium text-gray-500 dark:text-text-secondary">Jadwal Nonaktif</p>
                        <span class="text-4xl font-bold text-gray-900 dark:text-white mt-1">{{ $inactiveSchedules }}</span>
                    </div>
                    <svg class="h-7 w-7 text-red-600 dark:text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <p class="text-sm text-red-500 dark:text-red-400 mt-4 flex items-center">
                    <svg class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L12 8.414l3.293 3.293a1 1 0 001.414 0z" clip-rule="evenodd" /></svg>
                    -4% dari bulan lalu
                </p>
            </div>

            {{-- Kartu: Total Pengguna --}}
            <div class="bg-white dark:bg-dark-secondary p-6 rounded-xl border border-gray-200 dark:border-dark-tertiary transition-all duration-300 hover:border-sky-500">
                <div class="flex justify-between items-start">
                    <div class="flex flex-col">
                        <p class="text-sm font-medium text-gray-500 dark:text-text-secondary">Total Pengguna</p>
                        <span class="text-4xl font-bold text-gray-900 dark:text-white mt-1">{{ $userCount }}</span>
                    </div>
                    <svg class="h-7 w-7 text-sky-600 dark:text-sky-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.226A3 3 0 0113.12 12.24M11.25 18.75v-2.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v2.625M16.5 18.75m-3.75-2.625a3.375 3.375 0 00-3.375-3.375h-1.5a3.375 3.375 0 00-3.375 3.375m9.75 0v-2.625c0-.621-.504-1.125-1.125-1.125h-2.25c-.621 0-1.125.504-1.125 1.125v2.625m-7.5-2.226A3 3 0 016.38 12.24M6.38 15.72a9.094 9.094 0 013.741.479 3 3 0 01-4.682-2.72m-3.75-2.226A3.375 3.375 0 006.75 12.25h1.5a3.375 3.375 0 003.375-3.375M10.5 18.75v-2.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v2.625" /></svg>
                </div>
                <p class="text-sm text-green-500 dark:text-green-400 mt-4 flex items-center">
                    <svg class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L12 11.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    +2% lebih banyak
                </p>
            </div>
        </div>

        {{-- Statistik Sumber Daya (Gaya disamakan dengan kartu utama) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-dark-secondary p-6 rounded-xl border border-gray-200 dark:border-dark-tertiary text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-text-secondary uppercase tracking-wider">Dosen Terlibat</p>
                <p class="text-5xl font-extrabold text-gray-900 dark:text-white mt-2">{{ $uniqueTeachers }}</p>
            </div>
            <div class="bg-white dark:bg-dark-secondary p-6 rounded-xl border border-gray-200 dark:border-dark-tertiary text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-text-secondary uppercase tracking-wider">Ruangan Digunakan</p>
                <p class="text-5xl font-extrabold text-gray-900 dark:text-white mt-2">{{ $uniqueRooms }}</p>
            </div>
            <div class="bg-white dark:bg-dark-secondary p-6 rounded-xl border border-gray-200 dark:border-dark-tertiary text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-text-secondary uppercase tracking-wider">Kelas Terlibat</p>
                <p class="text-5xl font-extrabold text-gray-900 dark:text-white mt-2">{{ $uniqueClasses }}</p>
            </div>
        </div>

        {{-- Tabel Jadwal Terkini (Dibungkus dalam kartu terpadu) --}}
        <div class="bg-white dark:bg-dark-secondary rounded-xl border border-gray-200 dark:border-dark-tertiary mb-8">
            <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-dark-tertiary">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Jadwal Terkini</h2>
                <a href="{{ route('hasil.fet') }}" class="text-sm font-medium text-indigo-600 dark:text-brand-purple hover:underline">Lihat Semua Jadwal &raquo;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 dark:text-text-secondary uppercase">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">Mata Kuliah</th>
                        <th scope="col" class="px-6 py-3 font-medium">Dosen</th>
                        <th scope="col" class="px-6 py-3 font-medium">Kelas</th>
                        <th scope="col" class="px-6 py-3 font-medium">Hari & Jam</th>
                        <th scope="col" class="px-6 py-3 font-medium">Ruangan</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-900 dark:text-text-main">
                    @forelse ($recentSchedules as $schedule)
                        <tr class="border-t border-gray-200 dark:border-dark-tertiary hover:bg-gray-50 dark:hover:bg-dark-tertiary/20">
                            <td class="px-6 py-4 font-medium whitespace-nowrap">{{ $schedule->subject }}</td>
                            <td class="px-6 py-4">{{ $schedule->teacher }}</td>
                            <td class="px-6 py-4">
                                <span class="bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-300 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $schedule->kelas }}</span>
                            </td>
                            <td class="px-6 py-4">{{ optional($schedule->timeSlot)->day }}, {{ optional($schedule->timeSlot)->start_time }} - {{ optional($schedule->timeSlot)->end_time }}</td>
                            <td class="px-6 py-4">
                                <span class="bg-gray-200 text-gray-800 dark:bg-slate-600 dark:text-slate-300 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ optional($schedule->room)->name }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-10 text-gray-500 dark:text-text-secondary">Tidak ada jadwal.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- BAGIAN AKSI CEPAT & PANDUAN (GAYA TERPADU) --}}
        {{-- ======================================================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-dark-secondary rounded-xl border border-gray-200 dark:border-dark-tertiary p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Aksi Cepat</h2>
                <div class="space-y-3">
                    {{-- HIERARKI TOMBOL: Tombol utama (solid), sekunder (soft), dan tersier (outline) --}}

                    {{-- Tombol Utama --}}
                    <a href="#" class="w-full flex items-center justify-center py-2.5 px-4 rounded-lg font-semibold text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-brand-purple dark:hover:bg-purple-700 transition">
                        Atur Tahun Akademik
                    </a>

                    {{-- Tombol Sekunder (tidak merah agar tidak menakutkan) --}}
                    <button class="w-full flex items-center justify-center py-2.5 px-4 rounded-lg font-semibold text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:text-brand-purple dark:bg-brand-purple/20 dark:hover:bg-brand-purple/30 transition">
                        Unggah File FET Baru
                    </button>

                    {{-- Tombol Tersier/Outline --}}
                    <a href="{{ route('user.index') }}" class="w-full flex items-center justify-center py-2.5 px-4 rounded-lg font-semibold text-gray-700 bg-transparent border border-gray-300 hover:bg-gray-100 dark:text-text-secondary dark:border-dark-tertiary dark:hover:bg-dark-tertiary transition">
                        Kelola Pengguna
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-secondary rounded-xl border border-gray-200 dark:border-dark-tertiary p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Panduan & Sumber Daya</h2>
                <p class="text-gray-600 dark:text-text-secondary mb-4">Pelajari cara memaksimalkan penggunaan Fetnet atau temukan informasi tambahan.</p>
                <a href="{{ route('guide') }}" class="inline-flex items-center text-indigo-600 dark:text-brand-purple hover:underline font-medium">
                    Lihat Panduan Penggunaan
                    <svg class="w-4 h-4 ml-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.5a.75.75 0 01-1.5 0V3.75A.75.75 0 0110 3zm-2.25 9.75a.75.75 0 010 1.5H5.75a.75.75 0 010-1.5H7.75zM12.25 9.75a.75.75 0 010 1.5h-2a.75.75 0 010-1.5h2z" clip-rule="evenodd" /></svg>
                </a>
            </div>
        </div>

    </div>
@endsection
