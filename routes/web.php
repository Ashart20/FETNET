<?php

use App\Http\Controllers\Fakultas\GenerateController as FakultasGenerateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SimulationResultController; // <-- Import Controller Simulasi
// Livewire Components
use App\Livewire\Cluster\Dashboard as ClusterDashboard;
use App\Livewire\Cluster\Generate\Index as ClusterGenerateIndex;
// Fakultas Components
use App\Livewire\Cluster\ManageActivities as ClusterManageActivities;
use App\Livewire\Cluster\ViewSimulations;
use App\Livewire\Fakultas\ConflictChecker;
use App\Livewire\Faculty\Dashboard;
use App\Livewire\Fakultas\ManageActivityPreferredRooms;
use App\Livewire\Fakultas\ManageClusterUsers;
use App\Livewire\Fakultas\ManageProdi;
use App\Livewire\Fakultas\ManageRoomConstraints;
use App\Livewire\Fakultas\ManageRooms;
// Prodi Components
use App\Livewire\Fakultas\ViewSchedules as FakultasViewSchedules;
use App\Livewire\FetScheduleViewer;
use App\Livewire\Guide;
use App\Livewire\Mahasiswa\Dashboard as MahasiswaDashboard;
use App\Livewire\Prodi\Dashboard as ProdiDashboard;
use App\Livewire\Prodi\ManageActivities as ProdiManageActivities;
use App\Livewire\Prodi\ManageStudentGroupConstraints;
// Mahasiswa Components
use App\Livewire\Prodi\ManageStudentGroups;
// ==========================================================
// CLUSTER COMPONENTS (BARU)
// ==========================================================
use App\Livewire\Prodi\ManageSubjects;
use App\Livewire\Prodi\ManageTeacherConstraints;
use App\Livewire\Prodi\ManageTeachers;
use Illuminate\Support\Facades\Route;

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
Route::middleware('auth')->get('/hasil-fet', FetScheduleViewer::class)->name('hasil.fet');

// ==========================================================
// GRUP RUTE UNTUK FAKULTAS
// ==========================================================
Route::middleware(['auth', 'role:fakultas'])->prefix('fakultas')->name('fakultas.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/prodi', ManageProdi::class)->name('prodi');
    Route::get('/cluster-users', ManageClusterUsers::class)->name('cluster-users');
    Route::get('/ruangan', ManageRooms::class)->name('rooms');
    Route::get('/batasan-ruangan', ManageRoomConstraints::class)->name('room-constraints');
    Route::get('/preferensi-ruangan', ManageActivityPreferredRooms::class)->name('preferred-rooms');
    Route::get('/generate-jadwal', [FakultasGenerateController::class, 'index'])->name('generate.index');
    Route::post('/generate-jadwal', [FakultasGenerateController::class, 'generate'])->name('generate.store');
    Route::get('/jadwal', FakultasViewSchedules::class)->name('schedules.index');
    Route::get('/conflict-check', ConflictChecker::class)->name('conflict.index');
});

// ==========================================================
// GRUP RUTE UNTUK CLUSTER (BARU & LENGKAP)
// ==========================================================
Route::middleware(['auth', 'role:cluster'])->prefix('cluster')->name('cluster.')->group(function () {
    // Dashboard utama untuk user cluster
    Route::get('/dashboard', ClusterDashboard::class)->name('dashboard');

    // Halaman untuk mengelola aktivitas gabungan
    Route::get('/activities', ClusterManageActivities::class)->name('activities');

    // Halaman untuk memulai simulasi generate jadwal
    Route::get('/generate', ClusterGenerateIndex::class)->name('generate');

    // Halaman untuk melihat daftar hasil simulasi
    Route::get('/simulations', ViewSimulations::class)->name('simulations.index');

    // Halaman untuk menampilkan file HTML hasil simulasi
    Route::get('/simulations/view/{simulation_folder}/{file_name}', [SimulationResultController::class, 'show'])
        ->where('file_name', '.*') // Izinkan karakter '/' dalam parameter file_name
        ->name('simulations.show');
});

// ==========================================================
// GRUP RUTE UNTUK PRODI
// ==========================================================
Route::middleware(['auth', 'role:prodi'])->prefix('prodi')->name('prodi.')->group(function () {
    Route::get('/dashboard', ProdiDashboard::class)->name('dashboard');
    Route::get('/dosen', ManageTeachers::class)->name('teachers');
    Route::get('/matakuliah', ManageSubjects::class)->name('subjects');
    Route::get('/kelompok-mahasiswa', ManageStudentGroups::class)->name('student-groups');
    Route::get('/aktivitas', ProdiManageActivities::class)->name('activities');
    Route::get('/batasan-dosen', ManageTeacherConstraints::class)->name('teacher-constraints');
    Route::get('/batasan-mahasiswa', ManageStudentGroupConstraints::class)->name('student-group-constraints');
});

// ==========================================================
// GRUP RUTE UNTUK MAHASISWA
// ==========================================================
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', MahasiswaDashboard::class)->name('dashboard');
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
