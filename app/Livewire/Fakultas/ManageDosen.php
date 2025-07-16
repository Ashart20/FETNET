<?php

namespace App\Livewire\Fakultas;

use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;

class ManageDosen extends Component
{
    use WithPagination;
    public function render()
    {
        $teachers = Teacher::orderBy('nama_dosen')->paginate(13);
        return view('livewire.fakultas.manage-dosen',[
                'teachers' => $teachers,
            ])->layout('layouts.app');
    }
    public function headers(): array
    {
        return [
            ['key' => 'kode_dosen', 'label' => 'Kode Dosen', 'class' => 'w-1/12'],
            ['key' => 'full_name', 'label' => 'Nama Lengkap Dosen', 'sortable' => true],
            ['key' => 'prodi', 'label' => 'Prodi'],
            ['key' => 'kode_univ', 'label' => 'Kode UPI'],
            ['key' => 'actions', 'label' => '', 'class' => 'w-1'],
        ];
    }
}
