<div>
    {{-- Komponen Toast untuk notifikasi --}}
    <x-mary-toast />

    <div class="p-6 lg:p-8">
        {{-- Header halaman --}}
        <x-mary-header title="Data Dosen" subtitle="Manajemen data dan laporan beban SKS." separator />

        {{-- Tombol untuk Beralih Mode Tampilan --}}
        <div class="my-4">
            <x-mary-tabs wire:model.live="viewMode">
                <x-mary-tab name="manage" label="Mode Manajemen" icon="o-table-cells" />
                <x-mary-tab name="report" label="Laporan SKS" icon="o-chart-bar-square" />
            </x-mary-tabs>
        </div>

        {{--  Tampilan untuk MODE MANAJEMEN --}}
        @if ($viewMode === 'manage')
            <div class="space-y-6">
                {{-- Tombol Aksi Utama --}}
                <div class="flex flex-wrap gap-2">
                    <x-mary-button label="Tambah Dosen" icon="o-plus" class="btn-primary" @click="$wire.create()" />
                    <x-mary-button label="Unduh Template Excel" icon="o-document-arrow-down" class="btn-secondary" wire:click="downloadTemplate" spinner />
                </div>

                {{-- Tabel Data Dosen --}}
                <x-mary-table :headers="$headers" :rows="$teachers" with-pagination>
                    @scope('cell_nama_dosen', $teacher)
                    {{ $teacher->full_name }}
                    @endscope
                    @scope('actions', $teacher)
                    <div class="flex space-x-2">
                        <x-mary-button icon="o-pencil" @click="$wire.edit({{ $teacher->id }})" class="btn-sm btn-warning" tooltip="Edit" />
                        <x-mary-button icon="o-trash" wire:click="delete({{ $teacher->id }})" wire:confirm="PERHATIAN!|Anda yakin ingin menghapus dosen ini dari prodi Anda?" class="btn-sm btn-error" tooltip="Hapus" />
                    </div>
                    @endscope
                </x-mary-table>

                {{-- Fitur Impor Excel --}}
                <div class="p-4 bg-white dark:bg-gray-800/50 shadow-sm rounded-xl border dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Impor Data Dosen dari Excel</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Unggah file .xlsx untuk menambah atau memperbarui data dosen secara massal.
                    </p>
                    <div class="mt-4">
                        <x-mary-file wire:model.live="file" label="Pilih File Excel" hint="Hanya .xlsx" spinner />
                    </div>
                </div>
            </div>

            {{-- Tampilan untuk MODE LAPORAN SKS --}}
        @elseif ($viewMode === 'report')
            @if($teachers->isNotEmpty())
                <div class="overflow-x-auto rounded-lg border dark:border-gray-700">
                    <table class="table table-zebra table-pin-rows">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th rowspan="2" class="w-1">No.</th>
                            <th rowspan="2">Kode</th>
                            <th rowspan="2">Nama Dosen</th>
                            <th colspan="{{ auth()->user()->prodi->cluster->prodis->count() ?? 1 }}" class="text-center border-x dark:border-gray-700">Beban Mengajar (SKS)</th>
                            <th rowspan="2" class="text-center">Total</th>
                        </tr>
                        <tr>
                            @if(auth()->user()->prodi?->cluster)
                                @foreach(auth()->user()->prodi->cluster->prodis as $prodi)
                                    <th class="text-center border-x dark:border-gray-700">{{ $prodi->nama_prodi }}</th>
                                @endforeach
                            @else
                                <th class="text-center border-x dark:border-gray-700">{{ auth()->user()->prodi->nama_prodi }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($teachers as $index => $teacher)
                            <tr>
                                <td>{{ $teachers->firstItem() + $index }}.</td>
                                <td>{{ $teacher->kode_dosen }}</td>
                                <td>{{ $teacher->full_name }}</td>

                                @php($totalSKS = 0)
                                @if(auth()->user()->prodi?->cluster)
                                    @foreach(auth()->user()->prodi->cluster->prodis as $prodi)
                                        <td class="text-center border-x dark:border-gray-700">
                                            @php($sksPerProdi = $teacher->activities->where('prodi_id', $prodi->id)->sum('subject.sks'))
                                            {{ $sksPerProdi > 0 ? $sksPerProdi : '-' }}
                                            @php($totalSKS += $sksPerProdi)
                                        </td>
                                    @endforeach
                                @else
                                    <td class="text-center border-x dark:border-gray-700">
                                        @php($sksPerProdi = $teacher->activities->where('prodi_id', auth()->user()->prodi_id)->sum('subject.sks'))
                                        {{ $sksPerProdi > 0 ? $sksPerProdi : '-' }}
                                        @php($totalSKS += $sksPerProdi)
                                    </td>
                                @endif
                                <td class="text-center font-bold">{{ $totalSKS > 0 ? $totalSKS : '-' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $teachers->links() }}
                </div>
            @else
                <x-mary-alert title="Tidak ada data untuk ditampilkan." icon="o-information-circle" />
            @endif
        @endif
    </div>

    <x-mary-modal wire:model="teacherModal" title="{{ $teacherId ? 'Edit' : 'Tambah' }} Data Dosen" separator>
        <x-mary-form wire:submit="store">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="space-y-4">
                    <x-mary-input label="Gelar Depan" wire:model="title_depan" placeholder="Contoh: Dr." />
                    <x-mary-input label="Nama Lengkap" wire:model="nama_dosen" placeholder="Masukkan nama tanpa gelar" />
                    <x-mary-input label="Gelar Belakang" wire:model="title_belakang" placeholder="Contoh: M.Kom." />
                    <x-mary-input label="Kode Dosen (Prodi)" wire:model="kode_dosen" placeholder="Contoh: BDO, RMD" />
                </div>
                <div class="space-y-4">
                    <x-mary-input label="NIDN / Kode Universitas" wire:model="kode_univ" placeholder="Masukkan NIDN" />
                    <x-mary-input label="Employee ID / NIP" wire:model="employee_id" placeholder="Masukkan NIP" />
                    <x-mary-input label="Email" wire:model="email" type="email" placeholder="dosen@email.com" />
                    <x-mary-input label="Nomor HP" wire:model="nomor_hp" placeholder="08123456789" />
                </div>
            </div>
            <x-slot:actions>
                <x-mary-button label="Batal" @click="$wire.closeModal()" />
                <x-mary-button label="{{ $teacherId ? 'Update Data' : 'Simpan' }}" type="submit" class="btn-primary" spinner="store" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</div>
