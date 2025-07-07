<?php

namespace App\Livewire\Prodi;

use Livewire\Component;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Activity;
use Illuminate\View\View;
use Livewire\Attributes\Computed;

class Dashboard extends Component
{
    /**
     * Menggunakan Computed Property untuk mengambil data statistik.
     * Hasil dari metode ini akan di-cache untuk satu request.
     */
    #[Computed]
    public function stats(): array
    {
        $prodiId = auth()->user()->prodi_id;

        return [
            'totalDosen'     => Teacher::where('prodi_id', $prodiId)->count(),
            'totalMatkul'    => Subject::where('prodi_id', $prodiId)->count(),
            'totalAktivitas' => Activity::where('prodi_id', $prodiId)->count(),
        ];
    }

    public function render(): View
    {
        return view('livewire.prodi.dashboard')->layout('layouts.app');
    }
}
