<?php

namespace App\Livewire\Cluster;

use App\Models\Activity;
use App\Models\Prodi;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\View\View;

class Dashboard extends Component
{
    public array $stats = [];

    public function mount(): void
    {
        $this->loadStats();
    }

    public function loadStats(): void
    {
        $user = Auth::user();
        $clusterId = $user->cluster_id;

        // Inisialisasi statistik dengan nilai default
        $this->stats = [
            'totalProdi' => 0,
            'totalDosen' => 0,
            'totalAktivitas' => 0,
        ];

        // Jika user tidak terhubung ke cluster, jangan lakukan apa-apa
        if (!$clusterId) {
            return;
        }

        // Dapatkan semua ID prodi di dalam cluster ini
        $prodiIdsInCluster = Prodi::where('cluster_id', $clusterId)->pluck('id');

        if ($prodiIdsInCluster->isNotEmpty()) {
            // Hitung statistik berdasarkan prodi di dalam cluster
            $this->stats['totalProdi'] = $prodiIdsInCluster->count();

            $this->stats['totalDosen'] = Teacher::whereHas('prodis', function ($query) use ($prodiIdsInCluster) {
                $query->whereIn('prodis.id', $prodiIdsInCluster);
            })->distinct()->count();

            $this->stats['totalAktivitas'] = Activity::whereIn('prodi_id', $prodiIdsInCluster)->count();
        }
    }

    public function render(): View
    {
        return view('livewire.cluster.dashboard')->layout('layouts.app');
    }
}
