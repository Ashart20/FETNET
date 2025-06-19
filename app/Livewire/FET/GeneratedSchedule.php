<?php

namespace App\Livewire\Fet;

use Livewire\Component;

class GeneratedSchedule extends Component
{
    public $schedules = [];

    public function mount()
    {
        // Contoh data dummy, bisa diganti dari file FET / database
        $this->schedules = [
            [
                'kode_matkul' => 'EE592',
                'nama_matkul' => 'PRAKTIK KERJA',
                'sks' => 4,
                'kode_dosen' => '1235',
                'nama_dosen' => 'Dr. H. Bambang Trisno, M.SIE.',
                'kelas_info' => 'TE-2021\nRuang Smartclass 05.4A.04.025\nFPTK A Lt. 4\n40/5',
            ]
        ];
    }

    public function render()
    {
        return view('livewire.fet.generated-schedule');
// atau layouts.dashboard jika kamu punya

    }
}
