<?php

namespace App\Livewire\Prodi;

use App\Models\Activity;
use App\Models\Prodi;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function stats(): array
    {
        $prodi = auth()->user()->prodi;

        if (! $prodi) {
            return ['totalDosen' => 0, 'totalMatkul' => 0, 'totalAktivitas' => 0];
        }



        // [A] Ambil ID dosen dari dalam cluster
        $clusterTeacherIds = collect();
        if ($prodi->cluster_id) {
            $prodiIdsInCluster = Prodi::where('cluster_id', $prodi->cluster_id)->pluck('id');
            $clusterTeacherIds = DB::table('prodi_teacher')
                ->whereIn('prodi_id', $prodiIdsInCluster)
                ->pluck('teacher_id');
        }

        // [B] Ambil ID dosen yang terhubung manual
        $linkedTeacherIds = $prodi->teachers()->pluck('teachers.id');

        // [C] Gabungkan, hilangkan duplikat, dan hitung jumlahnya
        $totalDosen = $clusterTeacherIds->merge($linkedTeacherIds)->unique()->count();


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
