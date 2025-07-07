<div>
    <div class="p-6 lg:p-8">
        {{-- Bagian Header Sambutan --}}
        <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Selamat Datang, {{ auth()->user()->name }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Anda login sebagai admin untuk Program Studi: <strong>{{ auth()->user()->prodi->nama_prodi ?? 'N/A' }}</strong>.
            </p>
        </div>

        {{-- Kartu Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white">Total Dosen</h3>
                <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-2">{{ $this->stats['totalDosen'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white">Total Mata Kuliah</h3>
                <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-2">{{ $this->stats['totalMatkul'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white">Total Aktivitas</h3>
                <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-2">{{ $this->stats['totalAktivitas'] }}</p>
            </div>
        </div>

        {{-- Pintasan Navigasi --}}
        <div class="mt-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Menu Pengelolaan Data</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                <a href="{{ route('prodi.teachers') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white">Manajemen Dosen</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tambah, edit, dan hapus data dosen.</p>
                </a>
                <a href="{{ route('prodi.subjects') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white">Manajemen Matkul</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tambah, edit, dan hapus data mata kuliah.</p>
                </a>
                <a href="{{ route('prodi.student-groups') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white">Kelompok Mahasiswa</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data kelompok atau kelas mahasiswa.</p>
                </a>
                <a href="{{ route('prodi.activities') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white">Manajemen Aktivitas</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Rangkai dosen, matkul, dan kelompok.</p>
                </a>
                <a href="{{ route('prodi.teacher-constraints') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white">Batasan Waktu Dosen</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Atur jadwal ketersediaan para dosen.</p>
                </a>
                <a href="{{ route('prodi.student-group-constraints') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white">Batasan Waktu Mahasiswa</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Atur jadwal break para Mahasiswa.</p>
                </a>
            </div>
        </div>

        {{-- Tombol Aksi Utama --}}
        <div class="mt-8 p-6 lg:p-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Generate Jadwal</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Setelah semua data di atas lengkap dan benar, Anda dapat memulai proses pembuatan jadwal otomatis.
            </p>
            @if (session()->has('status'))
                <div class="bg-blue-100 dark:bg-blue-900/50 border-l-4 border-blue-500 dark:border-blue-600 text-blue-700 dark:text-blue-300 p-4 my-4 rounded-md" role="alert">
                    <p>{{ session('status') }}</p>
                </div>
            @endif
            <form action="{{ route('prodi.generate') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-2.25-1.313M21 7.5v2.25m0-2.25l-2.25 1.313M3 7.5l2.25-1.313M3 7.5l2.25 1.313M3 7.5v2.25m9 3l2.25-1.313M12 12.75l-2.25-1.313M12 12.75V15m0 6.75l2.25-1.313M12 21.75V19.5m0 2.25l-2.25-1.313m0-16.875L12 2.25l2.25 1.313M12 2.25L9.75 3.563m4.5 0L12 2.25M12 2.25v2.25m0 16.875a7.5 7.5 0 000-15H5.25a7.5 7.5 0 000 15h13.5z" />
                    </svg>
                    Mulai Generate Jadwal Otomatis
                </button>
            </form>
        </div>

    </div>
</div>
