<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Stage;
use Illuminate\Support\Arr;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            ['name' => 'بصيص', 'color' => '#9370DB','created_at' => now(), 'updated_at' => now()],
            ['name' => 'بريق', 'color' => '#FF69B4','created_at' => now(), 'updated_at' => now()],
            ['name' => 'ضياء', 'color' => '#ADFF2F','created_at' => now(), 'updated_at' => now()],
            ['name' => 'وميض', 'color' => '#DB7093','created_at' => now(), 'updated_at' => now()],
            ['name' => 'نور', 'color' => '#00CED1','created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($stages as $stage) {
            Stage::create(Arr::except($stage, []));
        }
    }
}
