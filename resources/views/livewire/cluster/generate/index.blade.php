<div>
    {{-- Komponen Toast untuk notifikasi instan --}}
    <x-mary-toast />

    <div class="p-4">
        {{-- Header Halaman --}}
        <x-mary-header title="Generate Jadwal (Simulasi Cluster)"
                       subtitle="Mulai proses pembuatan jadwal gabungan untuk semua prodi di dalam cluster Anda." />

        <div class="mt-6">
            <x-mary-card title="Mulai Proses Simulasi" icon="o-rocket-launch" shadow>
                <div class="space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Tombol di bawah ini akan memulai proses simulasi penjadwalan. Sistem akan membuat satu file `.fet` gabungan yang berisi semua data (dosen, matkul, aktivitas, batasan) dari **seluruh program studi di dalam cluster Anda**.
                    </p>
                    <x-mary-alert title="Penting!" description="Proses ini berjalan di latar belakang dan tidak akan menimpa jadwal resmi yang sedang berjalan. Hasil dari simulasi ini dapat dilihat di halaman 'Hasil Simulasi'." icon="o-exclamation-triangle" class="alert-warning">
                    </x-mary-alert>

                    {{-- Menampilkan notifikasi sukses atau error dari session --}}
                    @if (session('status'))
                        <x-mary-alert :description="session('status')" icon="o-information-circle" class="alert-info" />
                    @endif
                    @if (session('error'))
                        <x-mary-alert :description="session('error')" icon="o-exclamation-triangle" class="alert-error" />
                    @endif
                </div>

                {{-- Tombol Aksi --}}
                <x-slot:actions>
                    <x-mary-button
                        label="Mulai Simulasi untuk Cluster Ini"
                        wire:click="startGeneration"
                        class="btn-success"
                        icon="o-bolt"
                        spinner="startGeneration"
                        wire:confirm="Anda yakin ingin memulai proses simulasi? Ini mungkin akan memakan waktu beberapa saat." />
                </x-slot:actions>
            </x-mary-card>
        </div>
    </div>
</div>
