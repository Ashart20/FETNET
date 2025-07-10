<?php

namespace App\Livewire\Fakultas;

use App\Models\MasterRuangan;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function stats()
    {
        return [
            'totalProdi'     => Prodi::count(),
            'totalUserProdi' => User::role('prodi')->count(),
            'totalRuangan'   => MasterRuangan::count(),
        ];
    }

    public function render(): View
    {
        // PERBAIKAN: Hapus awalan "view:"
        return view('livewire.fakultas.dashboard')
            ->layout('layouts.app');
    }
}
