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
            ['name' => 'بصيص','created_at' => now(), 'updated_at' => now()],
            ['name' => 'بريق','created_at' => now(), 'updated_at' => now()],
            ['name' => 'ضياء','created_at' => now(), 'updated_at' => now()],
            ['name' => 'وميض','created_at' => now(), 'updated_at' => now()],
            ['name' => 'نور','created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($stages as $stage) {
            Stage::create(Arr::except($stage, []));
        }
    }
}
