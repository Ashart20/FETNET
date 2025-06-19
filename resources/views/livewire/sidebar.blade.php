<div x-data="{ open: true }" class="flex">
    <!-- Sidebar -->
    <aside
        :class="open ? 'w-64' : 'w-16'"
        class="fixed top-0 left-0 h-screen bg-gradient-to-b from-indigo-700 to-purple-700 text-white z-50 shadow-xl transition-all duration-300 ease-in-out overflow-hidden">

        <!-- Toggle Button -->
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center gap-3  " x-show="open">
                <img src="{{ asset('logo-fetnet.png') }}" class="w-11 h-11 bg-white rounded-full ring-0 ring-white" alt="Logo">
                <span class="text-xl font-bold italic tracking-wide">FETNET</span>
            </div>
            <button @click="open = !open" class="text-white gap-3 p-2 focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Nav Links -->
        <nav class="space-y-2 mt-6 px-2">
            <a href="#" class="flex items-center gap-3 p-4 rounded-lg hover:bg-indigo-600 transition-all">
                <i class="fas fa-calendar-alt w-5 text-indigo-200"></i>
                <span x-show="open" class="whitespace-nowrap">Dashboard</span>
            </a>
            <a href="{{ route('hasil.fet') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-600 transition-all">
                <i class="fas fa-users-cog w-5 text-indigo-200"></i>
                <span x-show="open">Hasil FET</span>
            </a>
            <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-600 transition-all">
                <i class="fas fa-users-cog w-5 text-indigo-200"></i>
                <span x-show="open">Manajemen Pengguna</span>
            </a>
            <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-600 transition-all">
                <i class="fas fa-book-open w-5 text-indigo-200"></i>
                <span x-show="open">Panduan Sistem</span>
            </a>
        </nav>
    </aside> \

    <!-- Main Content Wrapper -->
    <main :class="open ? 'ml-64' : 'ml-16'" class="transition-all duration-300 ease-in-out w-full">
    </main>
</div>
