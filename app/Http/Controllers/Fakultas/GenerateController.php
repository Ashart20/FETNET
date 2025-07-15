<?php

namespace App\Http\Controllers\Fakultas;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateFacultyTimetableJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GenerateController extends Controller
{
    /**
     * Menampilkan halaman untuk memulai proses generate.
     */
    public function index()
    {
        return view('livewire.fakultas.generate.index');
    }

    /**
     * Memulai proses generate jadwal untuk seluruh fakultas.
     */
    public function generate(Request $request)
    {
        // Dapatkan pengguna yang sedang login untuk referensi/logging jika diperlukan.

        // Memanggil Job. Job ini sekarang akan menangani seluruh proses untuk fakultas.
        GenerateFacultyTimetableJob::dispatch(auth()->id());

        // Memberikan feedback ke pengguna.
        return redirect()->route('fakultas.generate.index')
            ->with('status', 'Proses pembuatan jadwal untuk seluruh fakultas telah dimulai. Ini mungkin memakan waktu beberapa menit. Anda bisa memeriksa halaman "Jadwal Utama" secara berkala untuk melihat hasilnya.');
    }
}
