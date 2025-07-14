<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Pastikan untuk import User
use Spatie\Permission\Models\Role; // Import Hash untuk password

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Membuat Roles
        Role::create(['name' => 'fakultas']);
        Role::create(['name' => 'prodi']);
        Role::create(['name' => 'mahasiswa']);
        Role::firstOrCreate(['name' => 'cluster']);
        // Anda bisa tambahkan 'super-admin' jika perlu
        // Role::create(['name' => 'super-admin']);

        // (Opsional tapi sangat disarankan) Membuat user fakultas pertama
        // agar Anda bisa login setelah database di-refresh.
        $fakultasUser = User::create([
            'name' => 'Admin Fakultas',
            'email' => 'deewahyu@upi.edu',
            'password' => Hash::make('Ddw9889##'),
        ]);
        $fakultasUser->assignRole('fakultas');

    }
}
