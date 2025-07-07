<?php

namespace App\Livewire\Fakultas;

use App\Models\Building;
use App\Models\MasterRuangan;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ManageRooms extends Component
{
    use WithPagination;

    // Properti untuk form utama
    #[Rule('required|string|max:255')]
    public $nama_ruangan = '';

    #[Rule] // Aturan dinamis di method rules()
    public $kode_ruangan = '';

    #[Rule('required|exists:buildings,id')]
    public $building_id = '';

    #[Rule('required|string|max:50')]
    public $lantai = '';

    #[Rule('required|integer|min:1')]
    public $kapasitas = '';

    public $tipe = 'KELAS_TEORI';

    public $roomId;

    // Properti untuk data dropdown
    public Collection $buildings;

    // Properti untuk menambah gedung baru dari modal
    #[Rule('required|string|unique:buildings,name')]
    public $newBuildingName = '';

    #[Rule('required|string|unique:buildings,code')]
    public $newBuildingCode = '';

    public $isModalOpen = false;

    /**
     * Aturan validasi dinamis untuk kode ruangan.
     */
    public function rules()
    {
        return [
            'kode_ruangan' => 'required|string|max:50|unique:master_ruangans,kode_ruangan,' . $this->roomId,
        ];
    }

    /**
     * Pesan validasi kustom.
     */
    protected $messages = [
        'building_id.required' => 'Gedung wajib dipilih.',
        'newBuildingName.unique' => 'Nama gedung ini sudah ada.',
        'newBuildingCode.unique' => 'Kode gedung ini sudah ada.',
    ];

    public function mount()
    {
        $this->loadBuildings();
    }

    public function render()
    {
        $rooms = MasterRuangan::with('building')->latest()->paginate(10);

        return view('livewire.fakultas.manage-rooms', [
            'rooms' => $rooms
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        // Validasi untuk form ruangan utama
        $validatedData = $this->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kode_ruangan' => 'required|string|max:50|unique:master_ruangans,kode_ruangan,' . $this->roomId,
            'building_id' => 'required|exists:buildings,id',
            'lantai' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1',
            'tipe'         => 'required|string|in:KELAS_TEORI,LABORATORIUM,AUDITORIUM',
        ]);

        $validatedData['user_id'] = auth()->id();

        MasterRuangan::updateOrCreate(['id' => $this->roomId], $validatedData);

        session()->flash('message', $this->roomId ? 'Data Ruangan Berhasil Diperbarui.' : 'Data Ruangan Berhasil Ditambahkan.');
        $this->closeModal();
    }

    public function addNewBuilding()
    {
        // Validasi untuk form gedung baru saja
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

        // PERBAIKAN: Gunakan flash message untuk feedback yang lebih jelas
        session()->flash('building-message', 'Gedung baru berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $room = MasterRuangan::findOrFail($id);
        $this->roomId = $id;
        $this->nama_ruangan = $room->nama_ruangan;
        $this->kode_ruangan = $room->kode_ruangan;
        $this->building_id = $room->building_id;
        $this->tipe = $room->tipe;
        $this->lantai = $room->lantai;
        $this->kapasitas = $room->kapasitas;

        $this->openModal();
    }

    public function delete($id)
    {
        MasterRuangan::destroy($id);
        session()->flash('message', 'Data Ruangan Berhasil Dihapus.');
    }

    public function openModal() { $this->isModalOpen = true; }

    public function closeModal() {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->resetExcept('buildings'); // Jangan reset data dropdown gedung
        $this->resetErrorBag();
    }

    private function loadBuildings()
    {
        $this->buildings = Building::orderBy('name')->get();
    }
}
