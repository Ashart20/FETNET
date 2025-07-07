<?php

namespace App\Livewire\Fakultas;

use App\Models\MasterRuangan;
use App\Models\Prodi;
use App\Models\User;
use Livewire\Attributes\Computed; // Impor atribut Computed
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Menggunakan Computed Property.
     * Metode ini akan dijalankan sekali per request dan hasilnya di-cache.
     * Ini membuat properti tidak perlu didefinisikan di atas.
     */
    #[Computed]
    public function stats()
    {
        return [
            'totalProdi'     => Prodi::count(),
            'totalUserProdi' => User::role('prodi')->count(),
            'totalRuangan'   => MasterRuangan::count(),
        ];
    }

    public function render()
    {
        return view('livewire.fakultas.dashboard')->layout('layouts.app');
    }
}
