<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Day;
use App\Models\TimeSlot;

class TimeDataSeeder extends Seeder
{
    public function run(): void
    {
        // Buat data Hari
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat',];
        foreach ($days as $day) {
            Day::create(['name' => $day]);
        }

        // Buat data Slot Waktu
        $timeSlots = [
            ['name' => 'Jam ke-1', 'start_time' => '07:00', 'end_time' => '07:50'],
            ['name' => 'Jam ke-2', 'start_time' => '07:50', 'end_time' => '08:40'],
            ['name' => 'Jam ke-3', 'start_time' => '08:40', 'end_time' => '09:30'],
            ['name' => 'Jam ke-4', 'start_time' => '09:30', 'end_time' => '10:20'],
            ['name' => 'Jam ke-5', 'start_time' => '11:10', 'end_time' => '12:00'],
            ['name' => 'Jam ke-6', 'start_time' => '12:00', 'end_time' => '13:00'],
            ['name' => 'Jam ke-7', 'start_time' => '13:00', 'end_time' => '13:50'],
            ['name' => 'Jam ke-8', 'start_time' => '13:50', 'end_time' => '14:40'],
            ['name' => 'Jam ke-9', 'start_time' => '14:40', 'end_time' => '15:30'],
            ['name' => 'Jam ke-10', 'start_time' => '15:30', 'end_time' => '16:20'],
            ['name' => 'Jam ke-11', 'start_time' => '16:20', 'end_time' => '17:10'],
        ];
        foreach ($timeSlots as $slot) {
            TimeSlot::create($slot);
        }
    }
}
