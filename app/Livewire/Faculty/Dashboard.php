<?php

namespace App\Livewire\Faculty;

use App\Models\MasterRuangan;
use App\Models\Prodi;
use App\Models\User;
use App\Services\TimetableValidationService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Mary\Traits\Toast;

class Dashboard extends Component
{
    use Toast;

    // Properti untuk fitur validasi (ini yang terpakai)
    public array $validationIssues = [];
    public bool $hasBeenValidated = false;

    /**
     * Menghitung statistik yang benar-benar ditampilkan di Blade.
     */
    #[Computed]
    public function stats(): array
    {
        return [
            'totalProdi' => Prodi::count(),
            'totalUserProdi' => User::whereHas('roles', fn ($q) => $q->where('name', 'prodi'))->count(),
            'totalRuangan' => MasterRuangan::count(),
        ];
    }

    /**
     * Menjalankan service validasi dan menyimpan hasilnya.
     */
    public function runValidation()
    {
        $this->validationIssues = [];
        $this->hasBeenValidated = true;

        $validator = new TimetableValidationService();
        $this->validationIssues = $validator->validateAllData();

        if (empty($this->validationIssues)) {
            $this->success('Validasi Selesai', 'Selamat! Tidak ditemukan masalah pada data Anda.');
        } else {
            $this->warning('Validasi Selesai', 'Ditemukan beberapa potensi masalah pada data Anda.');
        }
    }

    public function render(): View
    {
        return view('livewire.faculty.dashboard')->layout('layouts.app');
    }
}
