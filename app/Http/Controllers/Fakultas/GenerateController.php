<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateFacultyTimetableJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GenerateController extends Controller
{
    public function index()
    {
        return view('livewire.fakultas.generate.index');
    }

    public function generate(Request $request)
    {
        $user = Auth::user(); // Dapatkan user fakultas yang sedang login

        // Memanggil Job dengan mengirimkan user fakultas
        GenerateFacultyTimetableJob::dispatch($user);

        return redirect()->route('fakultas.generate.index')
            ->with('status', 'Proses pembuatan jadwal untuk seluruh prodi telah dimulai. Ini mungkin memakan waktu beberapa menit. Anda bisa memeriksa halaman "Jadwal Utama" secara berkala.');
    }
}
