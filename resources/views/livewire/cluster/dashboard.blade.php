<div>
    {{-- Header Halaman --}}
    <x-mary-header title="Dasbor Cluster" subtitle="Ringkasan data dan pintasan navigasi untuk cluster Anda.">
        <x-slot:actions>
            <x-mary-button label="Generate Jadwal Cluster" icon="o-rocket-launch" link="{{ route('cluster.generate') }}" class="btn-primary" />
        </x-slot:actions>
    </x-mary-header>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <x-mary-stat
            title="Total Prodi di Cluster"
            :value="$this->stats['totalProdi']"
            icon="o-academic-cap"
            color="text-sky-500" />
        <x-mary-stat
            title="Total Dosen di Cluster"
            :value="$this->stats['totalDosen']"
            icon="o-users"
            color="text-amber-500" />
        <x-mary-stat
            title="Total Aktivitas di Cluster"
            :value="$this->stats['totalAktivitas']"
            icon="o-sparkles"
            color="text-green-500" />
    </div>

    {{-- Kartu Pintasan Navigasi --}}
    <x-mary-card title="Pintasan" class="mt-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-mary-button label="Manajemen Aktivitas" :link="route('cluster.activities')" icon="o-sparkles" class="h-24 btn-primary" />
            <x-mary-button label="Generate Jadwal" :link="route('cluster.generate')" icon="o-rocket-launch" class="h-24 btn-accent" />
            <x-mary-button label="Lihat Jadwal Utama" :link="route('hasil.fet')" icon="o-calendar-days" class="h-24 btn-success" />
        </div>
    </x-mary-card>
</div>
