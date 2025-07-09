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

    #[Rule] // Aturan validasi dinamis tetap di method rules()
    public string $kode_ruangan = '';

    #[Rule('required|exists:buildings,id', message: 'Gedung wajib dipilih.')]
    public string $building_id = '';

    #[Rule('required|string|max:50')]
    public string $lantai = '';

    #[Rule('required|integer|min:1')]
    public int $kapasitas = 10; // Beri nilai default

    #[Rule('required|string|in:KELAS_TEORI,LABORATORIUM,AUDITORIUM')]
    public string $tipe = 'KELAS_TEORI';

    public ?int $roomId = null; // Gunakan tipe data nullable

    // Properti untuk data dropdown
    public Collection $buildings;

    // Properti untuk menambah gedung baru dari modal
    #[Rule('required|string|unique:buildings,name', message: 'Nama gedung ini sudah ada.')]
    public string $newBuildingName = '';

    #[Rule('required|string|unique:buildings,code', message: 'Kode gedung ini sudah ada.')]
    public string $newBuildingCode = '';

    // 3. Ganti properti isModalOpen dengan ini untuk kontrol modal Mary UI
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
     * 4. Tambahkan method ini untuk mendefinisikan kolom tabel.
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
        // Ambil data ruangan dengan eager loading building
        $rooms = MasterRuangan::with('building')->latest()->paginate(10);

        return view('livewire.fakultas.manage-rooms', [
            'rooms' => $rooms,
        ])->layout('layouts.app');
    }

    public function updatedFile()
    {
        $this->validateOnly('file');

        try {
            // Proses impor file Excel
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
        $disk = 'local'; // storage/app

        // Data untuk template
        $data = [
            ['nama_ruangan', 'kode_ruangan', 'kode_gedung', 'lantai', 'kapasitas', 'tipe'], // Header
            ['B2-183-PTE', '183', 'C', '5', 40, 'KELAS_TEORI'],
            ['LAB ELKOM', '185', 'C', '4', 30, 'LABORATORIUM'],
        ];

        // Buat file menggunakan Export Class yang baru
        Excel::store(new RoomTemplateExport($data), $filename, $disk);

        // Unduh file menggunakan Storage facade
        return Storage::disk($disk)->download($filename);
    }

    public function create(): void
    {
        $this->resetInputFields();
        $this->roomModal = true; // Buka modal Mary UI
    }

    public function store(): void
    {
        // Validasi data utama
        $validatedData = $this->validate();

        // Tambahkan user_id jika ada user yang login
        $validatedData['user_id'] = auth()->id();

        MasterRuangan::updateOrCreate(['id' => $this->roomId], $validatedData);

        // 5. Gunakan notifikasi Toast dari Mary UI
        $message = $this->roomId ? 'Data Ruangan Berhasil Diperbarui.' : 'Data Ruangan Berhasil Ditambahkan.';
        $this->success($message);

        $this->closeModal(); // Tutup modal setelah berhasil
    }

    public function addNewBuilding(): void
    {
        $validated = $this->validate([
            'newBuildingName' => 'required|string|unique:buildings,name',
            'newBuildingCode' => 'required|string|unique:buildings,code',
        ]);

        $building = Building::create([
            'name' => $this->newBuildingName,
            'code' => $this->newBuildingCode,
        ]);

        $this->loadBuildings();
        $this->building_id = $building->id; // Langsung pilih gedung yang baru dibuat
        $this->reset(['newBuildingName', 'newBuildingCode']);

        // 6. Gunakan Toast untuk feedback
        $this->info('Gedung baru berhasil ditambahkan!', position: 'toast-bottom');
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

        $this->roomModal = true; // Buka modal Mary UI
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

    // Cukup ganti nama method agar lebih deskriptif
    public function closeModal(): void
    {
        $this->roomModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields(): void
    {
        $this->resetExcept('buildings'); // Jangan reset data dropdown
        $this->resetErrorBag();
    }

    private function loadBuildings(): void
    {
        $this->buildings = Building::orderBy('name')->get();
    }
}
