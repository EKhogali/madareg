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
                'password' => Hash::make('1'),
                'role' => User::ROLE_SUPER_ADMIN,
                'status' => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'elmothana.elmobarak@gmail.com'],
            [
                'name' => 'المثنى المبارك',
                'password' => Hash::make('MD#2026!!'),
                'role' => User::ROLE_SUPER_ADMIN,
                'status' => 1,
            ]
        );

        // ✅ Add: مديرة المشرفات (as Super Admin)
        User::updateOrCreate(
            ['email' => 'noramahmod201589@gmail.com'],
            [
                'name' => 'نورة',
                'password' => Hash::make('nmd#2024'),
                'role' => User::ROLE_SUPER_ADMIN,
                'status' => 1,
            ]
        );

        // ✅ Real Supervisors (from provided data)
        $supervisors = [
            ['name' => 'صفية',            'email' => 'safia.abdo352@gmail.com'],
            ['name' => 'بشرى عبد القادر', 'email' => 'bushraabdalkader4@gmail.com'],
            ['name' => 'أسماء',           'email' => 'asmaabdalqader26@gmail.com'], 
            ['name' => 'مريم',            'email' => 'marymabdalhamed2004@gmail.com'],
            ['name' => 'شهد إدريس',       'email' => 'Shahadidres32@gmail.com'],
            ['name' => 'كاميليا',         'email' => 'Zahratalkamilyaaffan@gmail.com'],
            ['name' => 'وفاء',            'email' => 'wafa.de94@gmail.com'],
            ['name' => 'مسرة',            'email' => 'Massarawahdy@gmail.com'],
            ['name' => 'رتال',            'email' => 'ritalidres4@gmail.com'],
            ['name' => 'صفاء',            'email' => 'safasalem.vo@gmail.com'],
            ['name' => 'بشرى الشويرف',    'email' => 'Bshwerif@gmail.com'],

            ['name' => 'رؤى',             'email' => 'rouashahrani05@gmail.com'],
            ['name' => 'فاطمة',           'email' => 'falslymany806@gmail.com'],
            ['name' => 'ريحانة',          'email' => 'riyhanaab9@gmail.com'],

            ['name' => 'غادة',            'email' => 'dodeezzdin@gmail.com'],
            ['name' => 'شفاء',            'email' => 'Shefaabdelbasit223@gmail.com'],
            ['name' => 'سهية',            'email' => 'sohaia2003@icloud.com'],

            ['name' => 'أحمد',            'email' => 'ahmed.supervisor@gmail.com'],
        ];

        foreach ($supervisors as $s) {
            User::updateOrCreate(
                ['email' => $s['email']],
                [
                    'name' => $s['name'],
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_SUPERVISOR,
                    'status' => 1,
                ]
            );
        }

    }
}
