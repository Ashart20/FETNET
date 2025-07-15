<div>
    <x-mary-toast />

    {{-- Header Halaman --}}
    <x-mary-header title="Dasbor Fakultas" subtitle="Ringkasan data dan pintasan navigasi.">
        <x-slot:actions>
            <x-mary-button label="Generate Jadwal" icon="o-sparkles" link="{{ route('fakultas.generate.index') }}" class="btn-primary" />
        </x-slot:actions>
    </x-mary-header>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <x-mary-stat
            title="Total Prodi"
            :value="$this->stats['totalProdi']"
            icon="o-academic-cap"
            color="text-sky-500" />
        <x-mary-stat
            title="Total User Prodi"
            :value="$this->stats['totalUserProdi']"
            icon="o-users"
            color="text-amber-500" />
        <x-mary-stat
            title="Total Ruangan"
            :value="$this->stats['totalRuangan']"
            icon="o-building-office-2"
            color="text-green-500" />
    </div>

    {{-- Pintasan Navigasi --}}
    <x-mary-card title="Pintasan" class="mt-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-mary-button label="Manajemen Prodi" :link="route('fakultas.prodi')" class="h-24 btn-primary" />
            <x-mary-button label="Manajemen Ruangan" :link="route('fakultas.rooms')" class="h-24 btn-accent" />
            <x-mary-button label="Batasan Ruangan" :link="route('fakultas.room-constraints')" class="h-24 btn-info" />
            <x-mary-button label="Lihat Jadwal" :link="route('fakultas.schedules.index')" icon="o-calendar-days" class="h-24 btn-success" />
        </div>
    </x-mary-card>

    {{-- BAGIAN BARU: VALIDASI INTEGRITAS DATA --}}
    <x-mary-card title="Validasi Integritas Data" subtitle="Periksa kelengkapan data sebelum generate jadwal." class="mt-8">
        <div class="p-2">
            <x-mary-button label="Jalankan Validasi Sekarang" wire:click="runValidation" icon="o-check-badge" class="btn-primary" spinner />
        </div>

        <div class="mt-4 space-y-4">
            @if ($hasBeenValidated)
                @forelse ($validationIssues as $issue)
                    @php
                        $isError = $issue['type'] === 'Error';
                        $bgColor = $isError ? 'bg-red-900/50 border-red-700' : 'bg-yellow-900/50 border-yellow-700';
                        $icon = $isError ? 'o-x-circle' : 'o-exclamation-triangle';
                    @endphp

                    <x-mary-alert :title="$issue['message']" :description="$issue['suggestion']" :icon="$icon" class="{{ $bgColor }}" />
                @empty
                    <x-mary-alert title="Semua Data Valid!" description="Tidak ditemukan masalah berdasarkan aturan validasi saat ini." icon="o-check-circle" class="alert-success" />
                @endforelse
            @else
                <x-mary-alert description="Hasil pengecekan data akan muncul di sini." icon="o-information-circle" />
            @endif
        </div>
    </x-mary-card>
</div>
