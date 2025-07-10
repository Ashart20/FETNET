<div>
    {{-- Menggunakan komponen Header untuk judul yang lebih rapi --}}
    <x-mary-header title="Dasbor Fakultas" subtitle="Ringkasan data dan pintasan navigasi.">
        <x-slot:actions>
            <x-mary-button label="Generate Jadwal" icon="o-sparkles" link="{{ route('fakultas.generate.index') }}" class="btn-primary" />
        </x-slot:actions>
    </x-mary-header>

    {{-- Mengganti div manual dengan komponen Stat --}}
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

    {{-- Mengganti link manual dengan komponen Card dan Button --}}
    <x-mary-card title="Pintasan" class="mt-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-mary-button label="Manajemen Prodi" :link="route('fakultas.prodi')" class="h-24 btn-primary" />
            <x-mary-button label="Manajemen Ruangan" :link="route('fakultas.rooms')" class="h-24 btn-accent" />
            <x-mary-button label="Batasan Ruangan" :link="route('fakultas.room-constraints')" class="h-24 btn-info" />
            <x-mary-button label="Lihat Jadwal" :link="route('fakultas.schedules.index')" icon="o-calendar-days" class="h-24 btn-success" />
        </div>
    </x-mary-card>
</div>
