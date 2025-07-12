<div>
    {{-- Komponen Toast untuk notifikasi --}}
    <x-mary-toast />

    <div class="p-6 lg:p-8">
        {{-- Header Halaman Mary UI --}}
        <x-mary-header title="Manajemen Data Dosen" subtitle="Kelola data dosen di program studi Anda.">
            {{-- Slot untuk tombol aksi di header --}}
            <x-slot:actions>
                <x-mary-button label="Tambah Dosen" @click="$wire.create()" class="btn-primary" icon="o-plus" />
            </x-slot:actions>
        </x-mary-header>

        {{-- Tabel Mary UI --}}
        {{--
        <x-mary-table :headers="$headers" :rows="$teachers" with-pagination>
            @scope('actions', $teacher)
            <div class="flex space-x-2">
                <x-mary-button icon="o-pencil" @click="$wire.edit({{ $teacher->id }})" class="btn-sm btn-warning" tooltip="Edit" />
                <x-mary-button icon="o-trash" wire:click="delete({{ $teacher->id }})" wire:confirm="PERHATIAN!|Anda yakin ingin menghapus data dosen ini?|Aksi ini tidak bisa dibatalkan." class="btn-sm btn-error" tooltip="Hapus" />
            </div>
            @endscope
        </x-mary-table>
        --}}
        @if($teachers->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <!-- head -->
                    <thead>
                    <tr>
                        <th rowsapn="2">No.</th>
                        <th rowsapn="2">Code</th>
                        <th rowsapn="2">Name</th>
                        <th colspan="5" class="text-center">Beban (SKS)</th>

                        <th rowsapn="2">Action</th>
                    </tr>
                    <tr>
                        <th colspan="3"></th>
                        @if(!is_null(auth()->user()->prodi->cluster))
                            @foreach(auth()->user()->prodi->cluster->prodis as $prodi)
                                <th class="text-center">{{$prodi->nama_prodi}}</th>
                            @endforeach
                        @endif
                        <th class="text-center">
                            Jumlah
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- row 1 -->
                    @foreach($teachers as $index => $teacher)
                        <tr>
                            <td>
                                {{$index+1}}.
                            </td>
                            <td>
                                {{$teacher->kode_dosen}}
                            </td>
                            <td>
                                {{$teacher->nama_dosen}}
                            </td>
                            @php($totalSKS = null)
                            @if(!is_null(auth()->user()->prodi->cluster))
                                @foreach(auth()->user()->prodi->cluster->prodis as $prodi)
                                    <td class="text-center">
                                        @php($sks = null)
                                        @foreach($teacher->activities as $activity)
                                            @if($activity->prodi->id == $prodi->id)
                                                @php($sks = $sks + $activity->subject->sks)
                                                @php($totalSKS = $totalSKS + $activity->subject->sks)
                                            @endif
                                        @endforeach
                                        {{$sks}}
                                    </td>
                                @endforeach
                            @endif
                            <td class="text-center">
                                {{$totalSKS}}
                            </td>
                            <td>

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            There is no data
        @endif
    </div>

    {{-- Modal Mary UI untuk form --}}
    <x-mary-modal wire:model="teacherModal" title="{{ $teacherId ? 'Edit' : 'Tambah' }} Data Dosen" separator>
        <x-mary-form wire:submit="store">
            <div class="space-y-4">
                <x-mary-input label="Nama Lengkap Dosen" wire:model="nama_dosen" placeholder="Masukkan nama lengkap beserta gelar" class="input-bordered" />
                <x-mary-input label="Kode Dosen" wire:model="kode_dosen" placeholder="Contoh: BDO, RMD" class="input-bordered" />
            </div>

            {{-- Slot untuk tombol aksi di modal --}}
            <x-slot:actions>
                <x-mary-button label="Batal" @click="$wire.closeModal()" />
                <x-mary-button label="{{ $teacherId ? 'Update Data' : 'Simpan' }}" type="submit" class="btn-primary" spinner="store" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</div>
