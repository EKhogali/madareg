<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use Illuminate\Support\Arr;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        // for ($i = 1; $i <= 5; $i++) {
        //     Group::create([
        //         'name' => "Group {$i}",
        //         'description' => "This is Group {$i} description.",
        //         'date_range_start' => now()->subDays(rand(5, 15)),
        //         'date_range_end' => now()->addDays(rand(10, 30)),
        //         'active' => true,
        //         'color' => '#'.substr(md5(rand()), 0, 6),
        //     ]);
        // }


        $groups = [
            [
                'name' => 'جبل الطوفان',
                'description' => 'أولاد + بنات ( أ ) | مواليد 2017 - 2018 | العدد 12 | المشرفات: ضيبه، بشرى عبد القادر، أسماء',
                'date_range_start' => '2017-01-01',
                'date_range_end' => '2018-12-31',
                'color' => '#FFC107',
                'supervisors' => ['ضيبه', 'بشرى عبد القادر', 'أسماء'],
                'member_count' => 12,
            ],
            [
                'name' => 'النسر',
                'description' => 'بنات ( ب ) | مواليد 2014 - 2016 | العدد 13 | المشرفات: مريم، شهد إدريس',
                'date_range_start' => '2014-01-01',
                'date_range_end' => '2016-12-31',
                'color' => '#E91E63',
                'supervisors' => ['مريم', 'شهد إدريس'],
                'member_count' => 13,
            ],
            [
                'name' => 'زُهاء',
                'description' => 'بنات ( ج ) | مواليد 2011 - 2013 | العدد 20 | المشرفات: وفاء، مسرة',
                'date_range_start' => '2011-01-01',
                'date_range_end' => '2013-12-31',
                'color' => '#9C27B0',
                'supervisors' => ['وفاء', 'مسرة'],
                'member_count' => 20,
            ],
            [
                'name' => 'زُهر',
                'description' => 'بنات ( د ) | مواليد 2008 - 2010 | العدد 30 | المشرفات: صفاء، بشرى الشريف',
                'date_range_start' => '2008-01-01',
                'date_range_end' => '2010-12-31',
                'color' => '#3F51B5',
                'supervisors' => ['صفاء', 'بشرى الشريف'],
                'member_count' => 30,
            ],
            [
                'name' => 'جنود الرحمن',
                'description' => 'أولاد ( ب ) | مواليد 2014 - 2016 | العدد 16 | المشرفات: زكى، شفاء، ريحانة',
                'date_range_start' => '2014-01-01',
                'date_range_end' => '2016-12-31',
                'color' => '#4CAF50',
                'supervisors' => ['زكى', 'شفاء', 'ريحانة'],
                'member_count' => 16,
            ],
            [
                'name' => 'غيث الأمة',
                'description' => 'أولاد ( ج ) | مواليد 2011 - 2013 | العدد 30 | المشرفات: عائدة، شفاء، سهى',
                'date_range_start' => '2011-01-01',
                'date_range_end' => '2013-12-31',
                'color' => '#009688',
                'supervisors' => ['عائدة', 'شفاء', 'سهى'],
                'member_count' => 30,
            ],
            [
                'name' => 'طلائع النور',
                'description' => 'أولاد ( د ) | مواليد 2008 - 2010 | العدد 13 | المشرف: أحمد',
                'date_range_start' => '2008-01-01',
                'date_range_end' => '2010-12-31',
                'color' => '#FF5722',
                'supervisors' => ['أحمد'],
                'member_count' => 13,
            ],
        ];
        foreach ($groups as $data) {
            Group::create(Arr::except($data, ['supervisors', 'member_count']));
        }

    }
}