<?php

namespace App\Livewire\Fakultas;

use App\Models\Building;
use App\Models\MasterRuangan;
use App\Imports\RoomsImport;
use App\Exports\RoomTemplateExport;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Support\Facades\Storage;
use Mary\Traits\Toast;

class ManageRooms extends Component
{
    use WithPagination;
    use Toast;
    use WithFileUploads;

    // Properti untuk form utama
    #[Rule('required|string|max:255')]
    public string $nama_ruangan = '';

    // Aturan validasi dinamis tetap di method rules()
    #[Rule]
    public string $kode_ruangan = '';

    // --- PERBAIKAN 1: Validasi building_id sekarang hanya wajib jika tidak membuat gedung baru ---
    #[Rule('required_without_all:newBuildingName,newBuildingCode|exists:buildings,id', message: 'Anda harus memilih gedung atau membuat yang baru.')]
    public string $building_id = '';

    #[Rule('required|string|max:50')]
    public string $lantai = '';

    #[Rule('required|integer|min:1')]
    public int $kapasitas = 10;

    #[Rule('required|string|in:KELAS_TEORI,LABORATORIUM,AUDITORIUM')]
    public string $tipe = 'KELAS_TEORI';

    public ?int $roomId = null;

    // Properti untuk data dropdown
    public Collection $buildings;

    // --- PERBAIKAN 2: Validasi gedung baru hanya wajib jika salah satu field diisi ---
    #[Rule('required_with:newBuildingCode|string|unique:buildings,name')]
    public string $newBuildingName = '';

    #[Rule('required_with:newBuildingName|string|unique:buildings,code')]
    public string $newBuildingCode = '';

    public bool $roomModal = false;
    public $file;

    /**
     * Aturan validasi dinamis untuk kode ruangan.
     */
    public function rules(): array
    {
        return [
            'kode_ruangan' => 'required|string|max:50|unique:master_ruangans,kode_ruangan,' . $this->roomId,
        ];
    }

    /**
     * Inisialisasi data saat komponen dimuat.
     */
    public function mount(): void
    {
        $this->loadBuildings();
    }

    /**
     * Mendefinisikan header untuk tabel Mary UI.
     */
    public function headers(): array
    {
        return [
            ['key' => 'nama_ruangan', 'label' => 'Nama Ruangan'],
            ['key' => 'kode_ruangan', 'label' => 'Kode'],
            ['key' => 'building.name', 'label' => 'Gedung'],
            ['key' => 'tipe', 'label' => 'Tipe'],
            ['key' => 'lantai', 'label' => 'Lantai'],
            ['key' => 'kapasitas', 'label' => 'Kapasitas'],
            ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-1'],
        ];
    }

    public function render()
    {
        $rooms = MasterRuangan::with('building')->latest()->paginate(10);
        return view('livewire.fakultas.manage-rooms', [
            'rooms' => $rooms,
        ])->layout('layouts.app');
    }

    /**
     * --- PERBAIKAN 3: Logika penyimpanan yang cerdas ---
     * Metode ini sekarang dapat menangani 2 skenario:
     * 1. Menyimpan ruangan dengan gedung yang sudah ada.
     * 2. Menyimpan ruangan DAN membuat gedung baru secara bersamaan.
     */
    public function store(): void
    {

        $this->validate();

        $finalBuildingId = $this->building_id;

        if (!empty($this->newBuildingName) && !empty($this->newBuildingCode)) {
            // Buat gedung baru
            $newBuilding = Building::create([
                'name' => $this->newBuildingName,
                'code' => $this->newBuildingCode,
            ]);

            // Gunakan ID gedung yang baru dibuat
            $finalBuildingId = $newBuilding->id;

            // Muat ulang daftar gedung untuk dropdown dan beri notifikasi
            $this->loadBuildings();
            $this->info('Gedung baru juga berhasil ditambahkan!');
        }

        // Pastikan ada ID gedung sebelum melanjutkan
        if (empty($finalBuildingId)) {
            $this->error('Gagal menyimpan.', 'Gedung tidak valid. Silakan pilih dari daftar atau buat yang baru.');
            return;
        }

        // Siapkan data ruangan untuk disimpan
        $roomData = [
            'nama_ruangan' => $this->nama_ruangan,
            'kode_ruangan' => $this->kode_ruangan,
            'building_id'  => $finalBuildingId,
            'lantai'       => $this->lantai,
            'kapasitas'    => $this->kapasitas,
            'tipe'         => $this->tipe,
            'user_id'      => auth()->id(),
        ];

        // Buat atau perbarui data ruangan
        MasterRuangan::updateOrCreate(['id' => $this->roomId], $roomData);

        $message = $this->roomId ? 'Data Ruangan Berhasil Diperbarui.' : 'Data Ruangan Berhasil Ditambahkan.';
        $this->success($message);

        $this->closeModal();
    }

    public function addNewBuilding(): void
    {
        $validated = $this->validate([
            'newBuildingName' => 'required|string|unique:buildings,name',
            'newBuildingCode' => 'required|string|unique:buildings,code',
        ]);

        $building = Building::create([
            'name' => $validated['newBuildingName'],
            'code' => $validated['newBuildingCode'],
        ]);

        // Muat ulang daftar gedung dan langsung pilih gedung yang baru dibuat
        $this->loadBuildings();
        $this->building_id = $building->id;

        // Kosongkan field input gedung baru
        $this->reset(['newBuildingName', 'newBuildingCode']);

        $this->info('Gedung baru berhasil ditambahkan! Silakan lanjutkan mengisi form ruangan.', position: 'toast-bottom');
    }

    public function updatedFile()
    {
        $this->validateOnly('file');
        try {
            Excel::import(new RoomsImport(), $this->file);
            $this->success('Data ruangan berhasil diimpor.', position: 'toast-bottom');
            $this->reset('file');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris ke-{$failure->row()}: " . implode(', ', $failure->errors());
            }
            $this->error('Impor Gagal. Ditemukan kesalahan:', implode("<br>", $errorMessages), timeout: 10000);
        } catch (\Exception $e) {
            $this->error('Impor Gagal.', 'Pastikan format dan header file Excel Anda sudah benar. ' . $e->getMessage(), timeout: 10000);
        }
    }

    public function downloadTemplate()
    {
        $filename = 'templates/template_ruangan.xlsx';
        $disk = 'local';
        $data = [
            ['nama_ruangan', 'kode_ruangan', 'kode_gedung', 'lantai', 'kapasitas', 'tipe'],
            ['B2-183-PTE', '183', 'C', '5', 40, 'KELAS_TEORI'],
            ['LAB ELKOM', '185', 'C', '4', 30, 'LABORATORIUM'],
        ];
        Excel::store(new RoomTemplateExport($data), $filename, $disk);
        return Storage::disk($disk)->download($filename);
    }

    public function create(): void
    {
        $this->resetInputFields();
        $this->roomModal = true;
    }

    public function edit(int $id): void
    {
        $room = MasterRuangan::findOrFail($id);
        $this->roomId = $id;
        $this->nama_ruangan = $room->nama_ruangan;
        $this->kode_ruangan = $room->kode_ruangan;
        $this->building_id = $room->building_id;
        $this->tipe = $room->tipe;
        $this->lantai = $room->lantai;
        $this->kapasitas = $room->kapasitas;
        $this->roomModal = true;
    }

    public function delete(int $id): void
    {
        try {
            MasterRuangan::destroy($id);
            $this->warning('Data Ruangan Berhasil Dihapus.');
        } catch (\Exception $e) {
            $this->error('Gagal menghapus ruangan. Mungkin terhubung dengan data lain.');
        }
    }

    public function closeModal(): void
    {
        $this->roomModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields(): void
    {
        $this->resetExcept('buildings');
        $this->resetErrorBag();
    }

    private function loadBuildings(): void
    {
        $this->buildings = Building::orderBy('name')->get();
    }
}
