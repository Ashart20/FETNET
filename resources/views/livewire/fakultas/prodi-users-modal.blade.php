<div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center pb-3 border-b dark:border-gray-600">
            {{-- PERBAIKAN: Judul modal dinamis --}}
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $userId ? 'Edit User Prodi' : 'Tambah User Prodi Baru' }}</p>
            <button wire:click="closeModal()" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 text-2xl">&times;</button>
        </div>

        <form wire:submit.prevent="store" class="pt-4">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nama User:</label>
                <input type="text" id="name" wire:model="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Email (untuk login):</label>
                <input type="email" id="email" wire:model="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Password:</label>
                <input type="password" id="password" wire:model="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" placeholder="{{ $userId ? 'Kosongkan jika tidak diubah' : '' }}">
                @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="prodi_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Program Studi:</label>
                <select id="prodi_id" wire:model="prodi_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">-- Pilih Prodi --</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>
                @error('prodi_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end pt-4">
                <button type="button" wire:click="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-2">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{-- PERBAIKAN: Teks tombol dinamis --}}
                    {{ $userId ? 'Update' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>
</div>
