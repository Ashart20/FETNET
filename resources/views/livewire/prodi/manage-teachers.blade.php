<div>
    <x-mary-toast />

    <div class="p-6 lg:p-8">
        <x-mary-header title="Data Dosen" subtitle="Manajemen data dan laporan beban SKS." separator />

        <div class="my-4">
            <x-mary-tabs wire:model.live="viewMode">
                <x-mary-tab name="manage" label="Mode Manajemen" icon="o-table-cells" />
                <x-mary-tab name="report" label="Laporan SKS" icon="o-chart-bar-square" />
            </x-mary-tabs>
        </div>

        @if ($viewMode === 'manage')
            <div class="space-y-6">
                <div class="flex flex-wrap gap-2">
                    <x-mary-button label="Tambah Dosen Baru" icon="o-plus" class="btn-primary" @click="$wire.create()" />
                    <x-mary-button label="Unduh Template" icon="o-document-arrow-down" class="btn-secondary" wire:click="downloadTemplate" spinner />
                </div>

                {{-- BLOK BARU: PENCARIAN & PENAMBAHAN DOSEN TAMU --}}
                <div class="p-4 border border-dashed border-gray-600 rounded-lg">
                    <p class="text-lg font-bold mb-3">Hubungkan Dosen Tamu / Luar Prodi</p>
                    <div class="flex items-center space-x-2">
                        <div class="flex-grow">
                            <x-mary-input
                                placeholder="Cari nama atau kode dosen dari seluruh universitas..."
                                wire:model.live.debounce.300ms="teacherSearch"
                                icon="o-magnifying-glass"
                                hint="Ketik minimal 3 huruf untuk memulai pencarian"
                            />
                        </div>
                        <x-mary-button label="Clear" @click="$wire.set('teacherSearch', '')" class="btn-sm btn-ghost" />
                    </div>

                    @if($teacherSearchResults->isNotEmpty())
                        <ul class="mt-2 space-y-1 bg-gray-800 p-2 rounded-lg max-h-48 overflow-y-auto">
                            @foreach($teacherSearchResults as $result)
                                <li class="flex justify-between items-center text-sm p-2 hover:bg-gray-700 rounded">
                                    <span>{{ $result->full_name }} ({{ $result->kode_dosen }})</span>
                                    <x-mary-button label="Hubungkan" wire:click="linkTeacher({{ $result->id }})" class="btn-xs btn-success" spinner />
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                {{-- AKHIR BLOK BARU --}}

                <x-mary-table :headers="$headers" :rows="$teachers" with-pagination>
                    @scope('cell_nama_dosen', $teacher)
                    {{ $teacher->full_name }}
                    @endscope
                    @scope('actions', $teacher)
                    <div class="flex space-x-2">
                        <x-mary-button icon="o-pencil" @click="$wire.edit({{ $teacher->id }})" class="btn-sm btn-warning" tooltip="Edit Data Dosen" />
                        <x-mary-button
                            icon="o-link-slash"
                            wire:click="unlinkTeacher({{ $teacher->id }})"
                            wire:confirm="PERHATIAN!|Yakin memutus hubungan dosen ini dari prodi Anda?|Data dosen tidak akan dihapus jika masih terhubung ke prodi lain."
                            class="btn-sm btn-error"
                            tooltip="Putus Hubungan" />
                    </div>
                    @endscope
                </x-mary-table>

                <div class="p-4 bg-white dark:bg-gray-800/50 shadow-sm rounded-xl border dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Impor Data Dosen dari Excel</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Unggah file .xlsx untuk menambah atau memperbarui data dosen secara massal.</p>
                    <div class="mt-4">
                        <x-mary-file wire:model.live="file" label="Pilih File Excel" hint="Hanya .xlsx" spinner />
                    </div>
                </div>
            </div>

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

    {{-- MODAL FORM UNTUK TAMBAH/EDIT DOSEN --}}
    <x-mary-modal wire:model="teacherModal" title="{{ $teacherId ? 'Edit' : 'Tambah' }} Data Dosen" separator>
        <x-mary-form wire:submit="store">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="space-y-4">
                    <x-mary-input label="Gelar Depan" wire:model="title_depan" placeholder="Contoh: Dr." />
                    <x-mary-input label="Nama Lengkap" wire:model="nama_dosen" placeholder="Masukkan nama tanpa gelar" required />
                    <x-mary-input label="Gelar Belakang" wire:model="title_belakang" placeholder="Contoh: M.Kom." />
                    <x-mary-input label="Kode Dosen (Prodi)" wire:model="kode_dosen" placeholder="Contoh: BDO, RMD" required />
                </div>
                <div class="space-y-4">
                    <x-mary-input label="Kode UPI" wire:model="kode_univ" placeholder="Masukkan kode UPI" />
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
