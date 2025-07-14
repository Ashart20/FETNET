<?php

namespace App\Http\Controllers\Prodi;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateFetFileJob;
use Illuminate\Http\RedirectResponse;

class JadwalGenerationController extends Controller
{
    public function generate(): RedirectResponse
    {
        $prodi = auth()->user()->prodi;

        if (! $prodi) {
            return back()->with('error', 'User Anda tidak terhubung dengan Program Studi manapun.');
        }

        // Kirim tugas ke antrian untuk diproses di latar belakang
        GenerateFetFileJob::dispatch($prodi);

        return back()->with('message', 'Proses generate jadwal telah dimulai! Ini bisa memakan waktu beberapa menit. Hasilnya akan muncul di halaman Jadwal Utama.');
    }
}
