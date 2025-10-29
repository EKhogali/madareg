<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Track_degree;

class TrackDegreeSeeder extends Seeder
{
    public function run(): void
    {
        $milestones = [
            ['range' => [1, 100], 'title' => 'إحماء'],
            ['range' => [101, 200], 'title' => 'خطوة 1'],
            ['range' => [201, 300], 'title' => 'خطوة 2'],
            ['range' => [301, 400], 'title' => 'مرحلة'],
            ['range' => [401, 500], 'title' => 'إنجاز'],
            ['range' => [501, 600], 'title' => 'تقدُّم'],
            ['range' => [601, 700], 'title' => 'ركض'],
            ['range' => [701, 800], 'title' => 'جري'],
            ['range' => [801, 900], 'title' => 'سباق'],
            ['range' => [901, 1000], 'title' => 'فوز'],
        ];

        foreach ($milestones as $milestone) {
            for ($i = $milestone['range'][0]; $i <= $milestone['range'][1]; $i++) {
                Track_degree::create([
                    'title' => $milestone['title'],
                ]);
            }
        }
    }
}

