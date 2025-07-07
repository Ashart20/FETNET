<div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center pb-3 border-b dark:border-gray-700">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $studentGroupId ? 'Edit' : 'Tambah' }} Data</p>
            <button wire:click="closeModal()" class="text-gray-500 hover:text-gray-800 dark:hover:text-gray-300 text-3xl font-bold focus:outline-none">&times;</button>
        </div>

        <form wire:submit.prevent="store" class="pt-4 space-y-4">
            {{-- Tampilkan informasi parent jika ada --}}
            @if($parentId)
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Menambahkan Sub-item di bawah: <span class="font-semibold">{{ \App\Models\StudentGroup::find($parentId)->nama_kelompok }}</span>
                </div>
            @endif

            {{-- Definisikan kelas umum untuk input dan label agar konsisten --}}
            @php
                $labelClasses = 'block font-medium text-sm text-gray-700 dark:text-gray-300';
                $inputClasses = 'block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
            @endphp

            <div>
                <label for="angkatan" class="{{ $labelClasses }}">Angkatan:</label>
                <input type="text" id="angkatan" wire:model="angkatan" class="{{ $inputClasses }}">
                @error('angkatan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="nama_kelompok" class="{{ $labelClasses }}">Nama (Tingkat/Kelompok/Sub):</label>
                <input type="text" id="nama_kelompok" wire:model="nama_kelompok" class="{{ $inputClasses }}">
                @error('nama_kelompok') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="kode_kelompok" class="{{ $labelClasses }}">Kode (Opsional):</label>
                <input type="text" id="kode_kelompok" wire:model="kode_kelompok" class="{{ $inputClasses }}">
                @error('kode_kelompok') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="jumlah_mahasiswa" class="{{ $labelClasses }}">Jumlah Mahasiswa:</label>
                <input type="number" id="jumlah_mahasiswa" wire:model="jumlah_mahasiswa" class="{{ $inputClasses }}">
                @error('jumlah_mahasiswa') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end pt-4 space-x-2">
                <button type="button" wire:click="closeModal()" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">Batal</button>
                <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150">
                    {{ $studentGroupId ? 'Update' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>
</div>
