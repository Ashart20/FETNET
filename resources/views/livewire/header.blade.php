<header class="flex items-center justify-between px-6 py-4 bg-white rounded-xl shadow-sm">
    <!-- Kiri: Judul dan sapaan -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            👋 Selamat Datang, Admin FETNet
        </h1>
        <p class="text-sm text-gray-500">Ringkasan dan statistik jadwal Anda hari ini</p>
    </div>

    <!-- Kanan: Aksi cepat & profil -->
    <div class="flex items-center space-x-4">
        <!-- Tombol Aksi -->
        <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
            + Jadwal Baru
        </button>

        <!-- Notifikasi -->
        <button class="relative text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-500 animate-ping"></span>
        </button>
<!-- Profile -->
        <div class="relative group">
            <button class="flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                <i class="fas fa-user-circle text-lg"></i>
                Admin FETNet
            </button>
            <!-- Dropdown (opsional jika interaktif) -->
            <div class="absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-lg p-2 hidden group-hover:block">
                <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-700">Profil</a>
                <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-700">Keluar</a>
            </div>
        </div>
    </div>
</header>
