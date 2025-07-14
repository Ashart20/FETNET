<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTagSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // TUGAS 1: PERBAIKI DATA LAMA
            $kelasTeoriTag = ActivityTag::firstOrCreate(['name' => 'KELAS_TEORI']);
            $oldTagIds = ActivityTag::whereIn('name', ['GANJIL', 'GENAP'])->pluck('id');

            if ($oldTagIds->isNotEmpty()) {
                Activity::whereIn('activity_tag_id', $oldTagIds)
                    ->update(['activity_tag_id' => $kelasTeoriTag->id]);

                ActivityTag::whereIn('id', $oldTagIds)->delete();
            }

            // TUGAS 2: SIAPKAN DATA BARU
            $finalTags = ['PILIHAN', 'PRAKTIKUM', 'SEPARATOR', 'KELAS_TEORI'];
            foreach ($finalTags as $tag) {
                ActivityTag::firstOrCreate(['name' => $tag]);
            }
        });
    }
}
