<?php

namespace App\Livewire\Cluster\Generate;

use Livewire\Component;
use App\Jobs\GenerateClusterTimetable;
use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    // Method untuk memulai proses generate
    public function startGeneration()
{
    $user = Auth::user();

    if (!$user->cluster_id) {
        session()->flash('error', 'Anda tidak terhubung ke cluster manapun.');
        return;
    }

    // Panggil Job baru dengan user dan cluster_id
    GenerateClusterTimetable::dispatch($user, $user->cluster_id);

    session()->flash('status', 'Proses simulasi jadwal untuk cluster Anda telah dimulai. Hasilnya akan tersedia dalam beberapa saat.');
}

    public function render()
    {
        return view('livewire.cluster.generate.index')->layout('layouts.app');
    }
}
