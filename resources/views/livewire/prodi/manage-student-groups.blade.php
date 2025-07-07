<div>
    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Struktur Kelompok Mahasiswa</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Kelola struktur Tingkat (Year), Kelompok (Group), dan Sub-Kelompok.
        </p>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 dark:bg-green-900/50 dark:border-green-800 dark:text-green-200 p-4 my-4 rounded-md" role="alert">
                <p>{{ session('message') }}</p>
            </div>
        @endif

        {{-- Tombol untuk menambah Tingkat/Year baru (level teratas) --}}
        <button wire:click="create(null)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-4">
            Tambah Tingkat (Year)
        </button>

        @if($isModalOpen)
            @include('livewire.prodi.student-group-modal')
        @endif

        {{-- Daftar hierarkis --}}
        <div class="border dark:border-gray-700 rounded-lg p-4">
            @forelse($groups as $group)
                {{-- Panggil komponen rekursif untuk setiap item level atas --}}
                @include('livewire.prodi.partials.student-group-item', ['group' => $group, 'level' => 0])
            @empty
                <p class="text-center text-gray-500 dark:text-gray-400">Belum ada data. Silakan tambahkan tingkat baru.</p>
            @endforelse
        </div>
    </div>
</div>
