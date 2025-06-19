<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Livewire\FetScheduleViewer;
use App\Livewire\ScheduleTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\UserIndex;
use App\Livewire\Guide;
use App\Livewire\FET\GeneratedSchedule;


// ========================
// Halaman Login
// ========================
// routes/web.php
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// ========================
// Landing Page
// ========================
Route::get('/', function () {
    $resources = [
        [
            'title' => 'Panduan Penggunaan',
            'description' => 'Dokumentasi lengkap penggunaan FetNet.',
            'link' => 'https://fetnet.example.com/docs',
        ],
        [
            'title' => 'Masuk ke dashboard',
            'description' => 'Lihat dan kelola jadwal perkuliahan Anda.',
            'link' => route('dashboard'),
        ],
    ];
    return view('welcome', compact('resources'));
});

// ========================
// Halaman Dashboard dan Autentikasi
// ========================
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/users', UserIndex::class)->name('user.index');
    Route::get('/guide', Guide::class)->name('guide');
    Route::get('/generated', GeneratedSchedule::class)->name('generated.schedule');

    // Profil User
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});

// Routes auth dari Breeze atau Jetstream
require __DIR__.'/auth.php';
Route::get('/hasil-fet', \App\Livewire\FetScheduleViewer::class)->name('hasil.fet');
