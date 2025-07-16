<?php

namespace App\Livewire\Prodi;

use App\Exports\SubjectTemplateExport;
use App\Imports\SubjectsImport;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Mary\Traits\Toast;

class ManageSubjects extends Component
{
    use Toast,  WithFileUploads, WithPagination;

    // Properti untuk form modal
    public ?int $subjectId = null;

    public string $nama_matkul = '';

    public string $kode_matkul = '';

    public ?int $sks = null;

    public ?int $semester = null;

    public $file;

    public bool $subjectModal = false;

    /**
     * Mendefinisikan aturan validasi secara dinamis.
     */
    public function rules()
    {
        return [
            'nama_matkul' => 'required|string|max:100',
            'kode_matkul' => [
                'required',
                'string',
                'min:3',
                Rule::unique('subjects')->where('prodi_id', auth()->user()->prodi_id)->ignore($this->subjectId),
            ],
            'sks' => 'required|integer|min:1|max:20',
            'semester' => 'required|integer|min:1|max:20',
        ];
    }

    /**
     * Pesan validasi kustom.
     */
    protected $messages = [
        'kode_matkul.required' => 'Kode mata kuliah wajib diisi.',
        'kode_matkul.unique' => 'Kode mata kuliah ini sudah ada di prodi Anda.',
        'nama_matkul.required' => 'Kode mata kuliah wajib diisi.',
        'sks.required' => 'Jumlah SKS wajib diisi.',
        'sks.integer' => 'SKS harus berupa angka.',
        'semester.required' => 'Semester wajib diisi.',
        'semester.integer' => 'Semester harus berupa angka.',
    ];

    /**
     * Mendefinisikan header untuk tabel Mary UI.
     * Mirip dengan manage-rooms.php
     */
    public function headers(): array
    {
        return [
            ['key' => 'kode_matkul', 'label' => 'Kode'],
            ['key' => 'nama_matkul', 'label' => 'Nama Mata Kuliah'],
            ['key' => 'sks', 'label' => 'SKS'],
            ['key' => 'semester', 'label' => 'Semester'],
            ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-1'],
        ];
    }

    public function render()
    {
        $subjects = Subject::where('prodi_id', auth()->user()->prodi_id)
            ->orderBy('semester', 'asc')
            ->orderBy('kode_matkul', 'asc')
            ->paginate(100);

        return view('livewire.prodi.manage-subjects', [
            'subjects' => $subjects,
        ])->layout('layouts.app');
    }

    public function updatedFile()
    {
        $this->validateOnly('file');

        try {
            Excel::import(new SubjectsImport(auth()->user()->prodi_id), $this->file);
            $this->success('Semua data mata kuliah berhasil diimpor.', position: 'toast-bottom'); // Gunakan Toast
            $this->reset('file');

        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris ke-{$failure->row()}: ".implode(', ', $failure->errors());
            }
            $this->error('Impor Gagal. Ditemukan kesalahan:', implode('<br>', $errorMessages), timeout: 10000); // Gunakan Toast
        } catch (\Exception $e) {
            $this->error('Impor Gagal.', 'Pastikan format dan header file Excel Anda sudah benar. Error: '.$e->getMessage(), timeout: 10000); // Gunakan Toast
        }
    }

    /**
     * Metode untuk mengunduh template Excel.
     * Mirip dengan manage-rooms.php
     */
    public function downloadTemplate()
    {
        $filename = 'templates/template_matkul.xlsx';
        $disk = 'local'; // storage/app

        // Data contoh untuk template Mata Kuliah
        $data = [
            ['nama_matkul', 'kode_matkul', 'sks'], // Header
            ['Algoritma dan Struktur Data', 'IF101', 3],
            ['Basis Data', 'IF202', 4],
        ];

        // Buat file menggunakan Export Class yang baru (SubjectTemplateExport)
        Excel::store(new SubjectTemplateExport($data), $filename, $disk);

        // Unduh file menggunakan Storage facade
        return Storage::disk($disk)->download($filename);
    }

    public function deleteAllSubjects()
    {
        Subject::where('prodi_id', auth()->user()->prodi_id)->delete();
        $this->warning('Semua data mata kuliah telah berhasil dihapus.'); // Gunakan Toast
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        $validatedData = $this->validate();
        $validatedData['prodi_id'] = auth()->user()->prodi_id;

        Subject::updateOrCreate(['id' => $this->subjectId], $validatedData);

        $message = $this->subjectId ? 'Data Mata Kuliah Berhasil Diperbarui.' : 'Data Mata Kuliah Berhasil Ditambahkan.';
        $this->success($message); // Gunakan Toast
        $this->closeModal();
    }

    public function edit($id)
    {
        $subject = Subject::where('prodi_id', auth()->user()->prodi_id)->findOrFail($id);

        $this->subjectId = $id;
        $this->nama_matkul = $subject->nama_matkul;
        $this->kode_matkul = $subject->kode_matkul;
        $this->sks = $subject->sks;
        $this->semester = $subject->semester;

        $this->openModal();
    }

    public function delete($id)
    {
        try {
            Subject::where('prodi_id', auth()->user()->prodi_id)->findOrFail($id)->delete();
            $this->warning('Data Mata Kuliah Berhasil Dihapus.'); // Gunakan Toast
        } catch (\Exception $e) {
            $this->error('Gagal menghapus mata kuliah. Mungkin terhubung dengan data lain.'); // Gunakan Toast
        }
    }

    public function openModal()
    {
        $this->subjectModal = true;
    }

    public function closeModal()
    {
        $this->subjectModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset();
        $this->resetErrorBag();
    }
}
