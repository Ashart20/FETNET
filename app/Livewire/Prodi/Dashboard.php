<?php

namespace App\Livewire\Prodi;

use Livewire\Component;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Activity;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use App\Models\Prodi; // Pastikan model Prodi diimport

class Dashboard extends Component
{
    #[Computed]
    public function stats(): array
    {
        $prodiId = auth()->user()->prodi_id;

        $prodi = Prodi::find($prodiId); // Dapatkan instance Prodi

        if (!$prodi) {
            // Handle jika prodi tidak ditemukan, misalnya user_id.prodi_id tidak valid
            return [
                'totalDosen'     => 0,
                'totalMatkul'    => 0,
                'totalAktivitas' => 0,
            ];
        }

        return [
            // BARIS INI YANG HARUS DIUBAH UNTUK totalDosen
            'totalDosen'     => $prodi->teachers()->count(), // Mengakses relasi many-to-many dari Prodi
            'totalMatkul'    => Subject::where('prodi_id', $prodiId)->count(),
            'totalAktivitas' => Activity::where('prodi_id', $prodiId)->count(),
        ];
    }

    public function render(): View
    {
        return view('livewire.prodi.dashboard')->layout('layouts.app');
    }
}
