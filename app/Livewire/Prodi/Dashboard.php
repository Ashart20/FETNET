<?php

namespace App\Livewire\Prodi;

use Livewire\Component;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Activity;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use App\Models\Prodi;

class Dashboard extends Component
{

    #[Computed]
    public function stats(): array
    {
        $prodi = auth()->user()->prodi;

        if (!$prodi) {
            return ['totalDosen' => 0, 'totalMatkul' => 0, 'totalAktivitas' => 0];
        }

        $totalDosen = 0;
        // Jika prodi tergabung dalam cluster
        if ($prodi->cluster_id) {
            // [1] Dapatkan semua ID prodi dalam cluster yang sama
            $prodiIdsInCluster = Prodi::where('cluster_id', $prodi->cluster_id)->pluck('id');

            // [2] Hitung semua dosen yang terhubung dengan prodi-prodi tersebut
            $totalDosen = Teacher::whereHas('prodis', function ($query) use ($prodiIdsInCluster) {
                $query->whereIn('prodis.id', $prodiIdsInCluster);
            })->count();
        } else {
            // Fallback jika prodi tidak punya cluster
            $totalDosen = $prodi->teachers()->count();
        }

        return [
            'totalDosen' => $totalDosen,
            'totalMatkul' => Subject::where('prodi_id', $prodi->id)->count(),
            'totalAktivitas' => Activity::where('prodi_id', $prodi->id)->count(),
        ];
    }
    public function render(): View
    {
        return view('livewire.prodi.dashboard')->layout('layouts.app');
    }
}
