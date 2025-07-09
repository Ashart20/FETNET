<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Livewire Components
use App\Livewire\FetScheduleViewer;
use App\Livewire\Guide;
use App\Livewire\Fakultas\Dashboard as FakultasDashboard;
use App\Livewire\Fakultas\ManageProdi;
use App\Livewire\Fakultas\ManageRooms;
use App\Livewire\Fakultas\ManageRoomConstraints;
use App\Livewire\Prodi\Dashboard as ProdiDashboard;
use App\Livewire\Prodi\ManageTeachers;
use App\Livewire\Prodi\ManageSubjects;
use App\Livewire\Prodi\ManageStudentGroups;
use App\Livewire\Prodi\ManageActivities;
use App\Livewire\Prodi\ManageTeacherConstraints;
use App\Livewire\Prodi\ManageStudentGroupConstraints;
use App\Livewire\Mahasiswa\Dashboard as MahasiswaDashboard;
use App\Http\Controllers\Prodi\JadwalGenerationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Landing Page (Publik)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Halaman Jadwal Utama (Bisa dilihat siapa saja yang sudah login)
Route::middleware('auth')->get('/guide', Guide::class)->name('guide');
Route::middleware('auth')->get('/user.index', Guide::class)->name('user.index');

Route::middleware(['auth', 'role:prodi|mahasiswa'])->get('/hasil-fet', FetScheduleViewer::class)->name('hasil.fet');
// ==========================================================
// GRUP RUTE UNTUK FAKULTAS
// ==========================================================
Route::middleware(['auth', 'role:fakultas'])->prefix('fakultas')->name('fakultas.')->group(function () {
    Route::get('/dashboard', FakultasDashboard::class)->name('dashboard');
    Route::get('/prodi', ManageProdi::class)->name('prodi');
    Route::get('/ruangan', ManageRooms::class)->name('rooms');
    Route::get('/batasan-ruangan', ManageRoomConstraints::class)->name('room-constraints');
});

// ==========================================================
// GRUP RUTE UNTUK PRODI
// ==========================================================
Route::middleware(['auth', 'role:prodi'])->prefix('prodi')->name('prodi.')->group(function () {
    Route::get('/dashboard', ProdiDashboard::class)->name('dashboard');
    Route::get('/dosen', ManageTeachers::class)->name('teachers');
    Route::get('/matakuliah', ManageSubjects::class)->name('subjects');
    Route::get('/kelompok-mahasiswa', ManageStudentGroups::class)->name('student-groups');
    Route::get('/aktivitas', ManageActivities::class)->name('activities');
    Route::get('/batasan-dosen', ManageTeacherConstraints::class)->name('teacher-constraints');
    Route::get('/batasan-mahasiswa', ManageStudentGroupConstraints::class)->name('student-group-constraints');
    Route::post('/generate', [JadwalGenerationController::class, 'generate'])->name('generate');
});

// ==========================================================
// GRUP RUTE UNTUK MAHASISWA
// ==========================================================
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    // Ganti 'ProdiDashboard' dengan Dasbor Mahasiswa jika sudah dibuat
    Route::get('/dashboard', MahasiswaDashboard::class)->name('dashboard');
    // Contoh: Route::get('/jadwal-saya', LihatJadwalMahasiswa::class)->name('jadwal.saya');
});


// ==========================================================
// RUTE BAWAAN LARAVEL (PROFIL, DLL)
// ==========================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Route authentication dari Breeze (login, register, dll.)
require __DIR__.'/auth.php';
