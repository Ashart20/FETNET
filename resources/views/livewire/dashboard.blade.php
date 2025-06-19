
<div class="flex min-h-screen bg-gray-50">
    <livewire:sidebar />

    <main class="p-6 w-full space-y-6">
        <livewire:header />

        {{-- Stats Cards --}}
        <livewire:stats-cards />

        {{-- Grid for main content --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Jadwal Terkini (2/3 width on large screen) --}}
            <div class="lg:col-span-2">
                <livewire:dashboard.jadwal-terkini />
            </div>

            {{-- Right: Statistik Pengguna --}}
            <div class="lg:col-span-1">
                <livewire:dashboard.statistik-pengguna />
            </div>
        </div>

        {{-- Panduan Cepat Section --}}
        <div>
            <livewire:dashboard.panduan-cepat />
        </div>
    </main>
</div>
