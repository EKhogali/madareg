<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Super Admins
        User::updateOrCreate(
            ['email' => 'ahmed10xty@gmail.com'],
            [
                'name' => 'احمد الحاسي',
                'password' => Hash::make('a@2024Hassi!'),
                'role' => User::ROLE_SUPER_ADMIN,
                'status' => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'elmothana.elmobarak@gmail.com'],
            [
                'name' => 'المثنى المبارك',
                'password' => Hash::make('qD9[R+A&"[4A~~+x'),
                'role' => User::ROLE_SUPER_ADMIN,
                'status' => 1,
            ]
        );

        // ✅ Supervisors list
        $supervisors = [
            'ضيبه',
            'بشرى عبد القادر',
            'أسماء',
            'مريم',
            'شهد إدريس',
            'وفاء',
            'مسرة',
            'صفاء',
            'بشرى الشريف',
            'زكى',
            'شفاء',
            'ريحانة',
            'عائدة',
            'سهى',
            'أحمد'
        ];

        foreach ($supervisors as $index => $name) {
            User::updateOrCreate(
                ['email' => 'supervisor' . ($index + 1) . '@example.com'],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_SUPERVISOR,
                    'status' => 1,
                ]
            );
        }

        // ✅ Parents (Members) => 120
        // for ($i = 1; $i <= 120; $i++) {
        //     User::updateOrCreate(
        //         ['email' => "member{$i}@example.com"],
        //         [
        //             'name' => "ولي أمر {$i}",
        //             'password' => Hash::make('password'),
        //             'role' => User::ROLE_MEMBER,
        //             'status' => 1,
        //         ]
        //     );
        // }

        $this->command->info('✅ Users seeded successfully (SuperAdmins + Supervisors + Parents).');
    }
}
