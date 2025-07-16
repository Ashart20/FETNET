<div>
    <x-mary-toast />

    <div class="p-6 lg:p-8">
        <x-mary-header title="Manajemen Dosen"
                       subtitle="Kelola data dosen semua prodi." />

        {{-- Tabel Merry UI --}}
        <x-mary-table :headers="$this->headers()" :rows="$teachers" with-pagination>
            @scope('cell_prodi', $teacher)
                {{$teacher->proditeacher}}
            @endscope
        </x-mary-table>

    </div>
</div>
