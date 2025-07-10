<div>
    <x-mary-toast />

    <div class="p-4">
        <x-mary-header title="Manajemen Struktur Kelompok Mahasiswa" subtitle="Kelola struktur Tingkat, Kelompok, dan Sub-Kelompok.">
            <x-slot:actions>
                <x-mary-button label="Tambah Tingkat (Year)" icon="o-plus" @click="$wire.create(null)" class="btn-primary" />
            </x-slot:actions>
        </x-mary-header>

        {{-- Daftar hierarkis dibungkus Card --}}
        <x-mary-card class="mt-4">
            @forelse($groups as $group)
                {{-- Panggil komponen rekursif untuk setiap item level atas --}}
                @include('livewire.prodi.partials.student-group-item', ['group' => $group, 'level' => 0])
            @empty
                <x-mary-alert title="Belum Ada Data" description="Silakan tambahkan tingkat (year) baru untuk memulai." icon="o-information-circle" />
            @endforelse
        </x-mary-card>
    </div>

    {{-- Modal Form --}}
    <x-mary-modal wire:model="studentGroupModal" title="{{ $studentGroupId ? 'Edit' : 'Tambah' }} Data" separator>
        <x-mary-form wire:submit="store">
            <div class="space-y-4">
                @if($parentId)
                    <x-mary-alert :description="'Menambahkan Sub-item di bawah: <strong>' . (\App\Models\StudentGroup::find($parentId)->nama_kelompok ?? '') . '</strong>'" class="alert-info" />
                @endif
                <x-mary-input label="Angkatan" wire:model="angkatan" placeholder="Contoh: 2023" class="input-bordered" />
                <x-mary-input label="Nama (Tingkat/Kelompok/Sub)" wire:model="nama_kelompok" placeholder="Contoh: TE 01" class="input-bordered" />
                <x-mary-input label="Kode (Opsional)" wire:model="kode_kelompok" class="input-bordered" />
                <x-mary-input label="Jumlah Mahasiswa" wire:model="jumlah_mahasiswa" type="number" class="input-bordered" />
            </div>

            <x-slot:actions>
                <x-mary-button label="Batal" @click="$wire.closeModal()" />
                <x-mary-button label="{{ $studentGroupId ? 'Update' : 'Simpan' }}" type="submit" class="btn-primary" spinner="store" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</div>
