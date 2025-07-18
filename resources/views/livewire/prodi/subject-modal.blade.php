<x-mary-form wire:submit="store">
    <div class="space-y-4">
        <x-mary-input label="Nama Mata Kuliah" wire:model="nama_matkul" class="input-bordered" />
        <x-mary-input label="Kode Mata Kuliah" wire:model="kode_matkul" class="input-bordered" />
        <x-mary-input label="Jumlah SKS" wire:model="sks" type="number" min="1" max="6" class="input-bordered" />
        <x-mary-input label="Semester" wire:model="semester" type="number" class="input-bordered" />
    </div>


    <x-slot:actions>
        <x-mary-button label="Batal" wire:click="closeModal" class="btn-ghost" />
        <x-mary-button label="{{ $subjectId ? 'Update' : 'Simpan' }}" type="submit" class="btn-primary" spinner="store" />
    </x-slot:actions>
</x-mary-form>
