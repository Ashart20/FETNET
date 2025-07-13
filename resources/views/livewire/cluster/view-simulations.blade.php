<div>
    <x-mary-toast />
    <x-mary-header title="Hasil Simulasi Jadwal" subtitle="Lihat dan terapkan hasil dari proses generate jadwal untuk cluster Anda.">
        <x-slot:actions>
            <x-mary-button label="Refresh Daftar" icon="o-arrow-path" wire:click="refreshList" class="btn-primary" spinner="refreshList" />
            <x-mary-button
                label="Hapus Semua"
                wire:click="deleteAllSimulations"
                icon="o-exclamation-triangle"
                wire:confirm="ANDA YAKIN? Semua hasil simulasi untuk cluster ini akan dihapus secara permanen. Aksi ini tidak bisa dibatalkan."
                class="btn-error" />
        </x-slot:actions>
    </x-mary-header>

    <div class="mt-4">
        @if(empty($simulations))
            <x-mary-alert title="Belum Ada Hasil" description="Anda belum pernah menjalankan simulasi, atau prosesnya belum selesai. Silakan mulai dari halaman 'Generate Jadwal'." icon="o-information-circle" />
        @else
            <x-mary-card>
                <div class="divide-y divide-base-200">
                    @foreach($simulations as $simulation)
                        <x-mary-list-item :item="$simulation" no-separator>
                            <x-slot:value>
                                <div class="font-bold">{{ $simulation['name'] }}</div>
                                <div class="text-xs opacity-70">Dibuat pada: {{ $simulation['created_at'] }}</div>
                            </x-slot:value>
                            <x-slot:actions>
                                <div class="flex items-center gap-2">
                                    <x-mary-button label="Lihat Hasil" :link="$simulation['url']" target="_blank" icon="o-arrow-top-right-on-square" class="btn-ghost btn-sm" />

                                    {{-- Tombol untuk menerapkan jadwal --}}
                                    <x-mary-button
                                        label="Terapkan Jadwal Ini"
                                        icon="o-check-badge"
                                        class="btn-success btn-sm"
                                        wire:click="applySimulation('{{ $simulation['folder'] }}')"
                                        wire:confirm="PERHATIAN!|Anda akan menimpa jadwal resmi untuk semua prodi di cluster ini.|Aksi ini tidak bisa dibatalkan. Lanjutkan?"
                                        spinner="applySimulation" />
                                    <x-mary-button
                                        icon="o-trash"
                                        wire:click="deleteSimulation('{{ $simulation['folder'] }}')"
                                        wire:confirm="Apakah Anda yakin ingin menghapus simulasi ini secara permanen?"
                                        class="btn-error btn-sm" />
                                </div>
                            </x-slot:actions>
                        </x-mary-list-item>
                    @endforeach
                </div>
            </x-mary-card>
        @endif
    </div>
</div>
