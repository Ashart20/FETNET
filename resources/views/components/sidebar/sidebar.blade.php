<div x-data="{ open: true }" class="flex">
    <!-- Sidebar -->
    <aside
        :class="open ? 'w-64' : 'w-16'"
        class="fixed top-0 left-0 h-screen bg-gradient-to-b from-indigo-700 to-purple-700 text-white z-50 shadow-xl transition-all duration-300 ease-in-out overflow-hidden">

        <!-- Toggle Button -->
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center gap-3" x-show="open">
                <img src="{{ asset('logo-fetnet.png') }}" class="w-10 h-10 rounded-full ring-2 ring-white" alt="Logo">
                <span class="text-xl font-bold italic tracking-wide">FETNET</span>
            </div>
            <button @click="open = !open" class="text-white focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Nav Links -->
        <nav class="space-y-2 mt-6 px-2">
            <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-600 transition-all">
                <i class="fas fa-calendar-alt w-5 text-indigo-200"></i>
                <span x-show="open" class="whitespace-nowrap">Dashboard</span>
            </a>
            <li>
                <a href="{{ route('schedule.generated') }}"
                   class="flex items-center px-4 py-2 hover:bg-indigo-600 hover:text-white transition-colors rounded-lg">
                    📋 <span class="ml-2">Jadwal Hasil FET</span>
                </a>
            </li>
            <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-600 transition-all">
                <i class="fas fa-users-cog w-5 text-indigo-200"></i>
                <span x-show="open">Manajemen Pengguna</span>
            </a>
            <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-600 transition-all">
                <i class="fas fa-book-open w-5 text-indigo-200"></i>
                <span x-show="open">Panduan Sistem</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <main :class="open ? 'ml-64' : 'ml-16'" class="transition-all duration-300 ease-in-out w-full">
        {{-- Konten utama di sini --}}
        <livewire:header />
        {{-- dst --}}
    </main>
</div>
