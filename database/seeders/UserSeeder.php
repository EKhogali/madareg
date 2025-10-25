<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Ahmed',
            'email' => 'a.hassi@gmail.com',
            'password' => Hash::make('a@2025'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        User::create([
            'name' => 'Elmo Super Admin',
            'email' => 'elmo@gmail.com',
            'password' => Hash::make('1'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        // Admin
        // User::create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@example.com',
        //     'password' => Hash::make('password'),
        //     'role' => User::ROLE_ADMIN,
        // ]);

        // // 3 Supervisors
        // for ($i = 1; $i <= 3; $i++) {
        //     User::create([
        //         'name' => "Supervisor {$i}",
        //         'email' => "supervisor{$i}@example.com",
        //         'password' => Hash::make('password'),
        //         'role' => User::ROLE_SUPERVISOR,
        //     ]);
        // }

        // // 5 Members
        // for ($i = 1; $i <= 5; $i++) {
        //     User::create([
        //         'name' => "Member {$i}",
        //         'email' => "member{$i}@example.com",
        //         'password' => Hash::make('password'),
        //         'role' => User::ROLE_MEMBER,
        //     ]);
        // }




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

        // Supervisors
        foreach ($supervisors as $index => $name) {
            User::create([
                'name' => $name,
                'email' => 'supervisor' . ($index + 1) . '@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPERVISOR,
            ]);
        }

        // Members (create 120 total random members)
        for ($i = 1; $i <= 120; $i++) {
            User::create([
                'name' => "عضو {$i}",
                'email' => "member{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => User::ROLE_MEMBER,
            ]);
        }

    }
}
