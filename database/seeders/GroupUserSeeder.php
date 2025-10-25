<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;

class GroupUserSeeder extends Seeder
{
    public function run(): void
    {
        // $groups = Group::all();
        // $supervisors = User::where('role', User::ROLE_SUPERVISOR)->get();
        // $members = User::where('role', User::ROLE_MEMBER)->get();

        // foreach ($groups as $group) {
        //     // attach one random supervisor to each group
        //     if ($supervisors->isNotEmpty()) {
        //         $group->users()->attach(
        //             $supervisors->random()->id
        //         );
        //     }

        //     // attach 2-3 random members to each group
        //     if ($members->count() >= 3) {
        //         $group->users()->attach(
        //             $members->random(rand(2, 3))->pluck('id')->toArray()
        //         );
        //     }
        // }





        $groups = [
            'جبل الطوفان' => ['ضيبه', 'بشرى عبد القادر', 'أسماء', 12],
            'النسر' => ['مريم', 'شهد إدريس', 13],
            'زُهاء' => ['وفاء', 'مسرة', 20],
            'زُهر' => ['صفاء', 'بشرى الشريف', 30],
            'جنود الرحمن' => ['زكى', 'شفاء', 'ريحانة', 16],
            'غيث الأمة' => ['عائدة', 'شفاء', 'سهى', 30],
            'طلائع النور' => ['أحمد', 13],
        ];

        $members = User::where('role', User::ROLE_MEMBER)->get();

        foreach ($groups as $groupName => $data) {
            $group = Group::where('name', $groupName)->first();
            if (!$group)
                continue;

            $supervisorNames = array_slice($data, 0, -1);
            $memberCount = end($data);

            // Attach supervisors
            $supervisors = User::whereIn('name', $supervisorNames)->pluck('id');
            $group->users()->attach($supervisors);

            // Attach random members based on required count
            $randomMembers = $members->random(min($memberCount, $members->count()))->pluck('id');
            $group->users()->attach($randomMembers);
        }

    }
}
