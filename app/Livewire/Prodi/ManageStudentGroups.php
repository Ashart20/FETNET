<?php

namespace App\Livewire\Prodi;

use Livewire\Component;
use App\Models\StudentGroup;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Mary\Traits\Toast;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule as ValidationRule;

class ManageStudentGroups extends Component
{
    use WithPagination, Toast;

    public ?int $studentGroupId = null;

    // PERBAIKAN: Gunakan nama_kelompok sesuai DB
    #[Rule('required|string|max:255')]
    public string $nama_kelompok = ''; // Diubah dari $name

    #[Rule('required|string|max:255')] // Validasi string karena di DB string
    public string $angkatan = '';

    #[Rule('nullable|exists:student_groups,id', message: 'Parent group tidak valid.')]
    public ?int $parent_id = null;

    public bool $isModalOpen = false;

    public Collection $parentGroups;

    public function rules()
    {
        return [
            // PERBAIKAN: Validasi unik untuk nama_kelompok
            'nama_kelompok' => [ // Diubah dari 'name'
                'required',
                'string',
                'max:255',
                ValidationRule::unique('student_groups')->where(function ($query) {
                    return $query->where('prodi_id', auth()->user()->prodi_id)
                        ->where('angkatan', $this->angkatan)
                        ->where('parent_id', $this->parent_id);
                })->ignore($this->studentGroupId),
            ],
            'angkatan' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:student_groups,id',
        ];
    }

    protected $messages = [
        'nama_kelompok.required' => 'Nama kelompok mahasiswa wajib diisi.', // Diubah dari 'name.required'
        'nama_kelompok.unique'   => 'Nama kelompok ini sudah ada di angkatan dan level ini.', // Diubah dari 'name.unique'
        'angkatan.required'     => 'Angkatan wajib diisi.',
        'angkatan.string'       => 'Angkatan harus berupa teks.',
        'parent_id.exists'      => 'Kelompok induk tidak valid.',
    ];


    public function mount()
    {
        $this->angkatan = date('Y');
        $this->loadParentGroups();
    }

    public function headers(): array
    {
        return [
            ['key' => 'nama_kelompok', 'label' => 'Nama Kelompok'], // Diubah dari 'name'
            ['key' => 'angkatan', 'label' => 'Angkatan'],
            ['key' => 'parent_group_name', 'label' => 'Bagian dari'],
            ['key' => 'students_count', 'label' => 'Jml. Mhs'],
            ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-1'],
        ];
    }


    public function render()
    {
        $prodiId = auth()->user()->prodi_id;

        $studentGroups = StudentGroup::where('prodi_id', $prodiId)
            ->with('parent')
            ->withCount('students')
            ->orderBy('angkatan', 'desc')
            ->orderBy('parent_id')
            ->orderBy('nama_kelompok') // PERBAIKAN: Urutkan berdasarkan nama_kelompok
            ->paginate(10);

        $studentGroups->getCollection()->transform(function ($group) {
            $group->parent_group_name = $group->parent ? $group->parent->nama_kelompok . ' (' . $group->parent->angkatan . ')' : 'Utama'; // PERBAIKAN
            return $group;
        });


        return view('livewire.prodi.manage-student-groups', [
            'studentGroups' => $studentGroups,
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->angkatan = date('Y');
        $this->openModal();
    }

    public function store()
    {
        $validatedData = $this->validate();
        $validatedData['prodi_id'] = auth()->user()->prodi_id;

        // PERBAIKAN: Pastikan Anda mengisi nama_kelompok
        $dataToStore = [
            'nama_kelompok' => $validatedData['nama_kelompok'],
            'angkatan' => $validatedData['angkatan'],
            'parent_id' => $validatedData['parent_id'],
            'prodi_id' => $validatedData['prodi_id'],
        ];
        // Tambahkan kolom lain jika ada di $fillable dan tidak null
        if (isset($validatedData['kode_kelompok'])) {
            $dataToStore['kode_kelompok'] = $validatedData['kode_kelompok'];
        }
        if (isset($validatedData['jumlah_mahasiswa'])) {
            $dataToStore['jumlah_mahasiswa'] = $validatedData['jumlah_mahasiswa'];
        } else {
            $dataToStore['jumlah_mahasiswa'] = 0; // Default jika tidak ada input
        }


        StudentGroup::updateOrCreate(['id' => $this->studentGroupId], $dataToStore);

        $this->toast(type: 'success', title: $this->studentGroupId ? 'Data Kelompok Mahasiswa Berhasil Diperbarui.' : 'Data Kelompok Mahasiswa Berhasil Ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $prodiId = auth()->user()->prodi_id;
        $group = StudentGroup::where('prodi_id', $prodiId)->findOrFail($id);

        $this->studentGroupId = $id;
        $this->nama_kelompok = $group->nama_kelompok; // Diubah dari $name
        $this->angkatan = $group->angkatan;
        $this->parent_id = $group->parent_id;
        // Jika Anda juga mengelola kode_kelompok atau jumlah_mahasiswa di form edit:
        // $this->kode_kelompok = $group->kode_kelompok;
        // $this->jumlah_mahasiswa = $group->jumlah_mahasiswa;

        $this->openModal();
    }

    public function delete($id)
    {
        try {
            $prodiId = auth()->user()->prodi_id;
            $group = StudentGroup::where('prodi_id', $prodiId)->findOrFail($id);

            if ($group->children()->exists()) {
                $this->error('Gagal menghapus kelompok. Kelompok ini memiliki sub-kelompok.');
                return;
            }

            if ($group->activities()->exists()) {
                $this->error('Gagal menghapus kelompok. Kelompok ini terkait dengan aktivitas yang ada.');
                return;
            }

            if ($group->students()->exists()) {
                $this->error('Gagal menghapus kelompok. Kelompok ini masih memiliki mahasiswa terdaftar.');
                return;
            }

            $group->delete();
            $this->toast(type: 'warning', title: 'Data Kelompok Mahasiswa Berhasil Dihapus.');
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan saat menghapus kelompok. ' . $e->getMessage());
        }
    }

    public function openModal() {
        $this->isModalOpen = true;
        $this->loadParentGroups();
    }

    public function closeModal() {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset(['studentGroupId', 'nama_kelompok', 'parent_id']); // Diubah dari 'name'
        $this->angkatan = date('Y');
        $this->resetErrorBag();
    }

    private function loadParentGroups()
    {
        $prodiId = auth()->user()->prodi_id;
        $this->parentGroups = StudentGroup::where('prodi_id', $prodiId)
            ->when($this->studentGroupId, function ($query) {
                $query->where('id', '!=', $this->studentGroupId);
            })
            ->where(function($query) {
                $query->whereNull('parent_id')
                    ->orWhereHas('children');
            })
            ->orderBy('angkatan', 'desc') // Mengurutkan berdasarkan angkatan
            ->orderBy('nama_kelompok') // Mengurutkan berdasarkan nama_kelompok
            ->get();

        // Mengubah nama opsi agar sesuai dengan nama_kelompok dan angkatan
        $this->parentGroups->prepend(new StudentGroup(['id' => null, 'nama_kelompok' => 'Tidak Ada (Kelompok Utama)', 'angkatan' => '']));
    }
}
