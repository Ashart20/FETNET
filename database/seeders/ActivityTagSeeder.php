<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\ActivityTag;

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
