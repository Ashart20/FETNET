<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Day;
use App\Models\TimeSlot;

class TimeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Data Hari
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        foreach ($days as $dayName) {
            // Mencari hari berdasarkan 'name'. Jika tidak ada, buat baru.
            // Jika sudah ada, tidak melakukan apa-apa (karena tidak ada data lain untuk di-update).
            Day::updateOrCreate(['name' => $dayName]);
        }

        // Data Slot Waktu
        $timeSlots = [
            ['name' => 'Jam ke-1', 'start_time' => '07:00', 'end_time' => '07:50'],
            ['name' => 'Jam ke-2', 'start_time' => '07:50', 'end_time' => '08:40'],
            ['name' => 'Jam ke-3', 'start_time' => '08:40', 'end_time' => '09:30'],
            ['name' => 'Jam ke-4', 'start_time' => '09:30', 'end_time' => '10:20'],
            ['name' => 'Jam ke-5', 'start_time' => '10:20', 'end_time' => '11:10'],
            ['name' => 'Jam ke-6', 'start_time' => '11:10', 'end_time' => '12:00'],
            ['name' => 'Jam ke-7', 'start_time' => '12:00', 'end_time' => '13:00'],
            ['name' => 'Jam ke-8', 'start_time' => '13:00', 'end_time' => '13:50'],
            ['name' => 'Jam ke-9', 'start_time' => '13:50', 'end_time' => '14:40'],
            ['name' => 'Jam ke-10', 'start_time' => '14:40', 'end_time' => '15:30'],
            ['name' => 'Jam ke-11', 'start_time' => '15:30', 'end_time' => '16:20'],
            ['name' => 'Jam ke-12', 'start_time' => '16:20', 'end_time' => '17:10'],
            ['name' => 'Jam ke-13', 'start_time' => '17:10', 'end_time' => '18:00'],
            // Tambahkan slot baru Anda di sini jika ada
        ];

        foreach ($timeSlots as $slot) {
            // Mencari slot berdasarkan 'name'.
            // Jika ditemukan, akan di-update dengan start_time dan end_time yang baru.
            // Jika tidak ditemukan, akan dibuat baris baru.
            TimeSlot::updateOrCreate(
                ['name' => $slot['name']], // Kunci untuk mencari
                [
                    'start_time' => $slot['start_time'], // Nilai untuk di-update atau dibuat
                    'end_time' => $slot['end_time']
                ]
            );
        }
    }
}
