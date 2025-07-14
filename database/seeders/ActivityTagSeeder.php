<?php

namespace Database\Seeders;

use App\Models\ActivityTag;
use Illuminate\Database\Seeder;

class ActivityTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = ['PILIHAN', 'PRAKTIKUM', 'SEPARATOR', 'GANJIL', 'GENAP'];
        foreach ($tags as $tag) {
            ActivityTag::create(['name' => $tag]);
        }
    }
}
