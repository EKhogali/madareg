<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subscriber;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\FollowUpTemplate;

class SubscriberSeeder extends Seeder
{
    public function run(): void
    {

        // Fetch all users with Member role (4)
        $members = User::where('role', 4)->get();

        if ($members->isEmpty()) {
            $this->command->warn('⚠️ No member users found. Please seed users first.');
            return;
        }

        $studyLevels = ['الصف الأول', 'الصف الثاني', 'الصف الثالث', 'الصف الرابع', 'الصف الخامس', 'الصف السادس'];
        $educationTypes = [0, 1, 2]; // 0=public, 1=private, 2=international
        $socialStatuses = [0, 1, 2, 3, 4, 5, 6];
        $fatherJobTypes = [0, 1, 2, 3];
        $motherJobTypes = [0, 1, 2, 3];
        $healthStatuses = [0, 1];

        foreach ($members as $user) {
            Subscriber::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'birth_date' => fake()->dateTimeBetween('-18 years', '-8 years'),
                'birth_place' => fake()->city(),
                'residence_place' => fake()->city(),
                'nationality' => 'ليبية',

                // Education
                'study_level' => Arr::random($studyLevels),
                'education_type' => Arr::random($educationTypes),
                'school_name' => 'مدرسة ' . fake()->word(),

                // Quran & handwriting
                'is_quran_student' => fake()->boolean(70), // 70% chance yes
                'quran_amount' => Arr::random(['جزء', 'جزئين', '5 أجزاء', '10 أجزاء', '15 جزء']),
                'quran_memorization_center' => fake()->boolean(70) ? 'مركز النور' : null,

                // Talents
                'talents' => Arr::random(['الخط العربي', 'الرسم', 'الإنشاد', 'التمثيل', 'الرياضة', 'البرمجة']),

                // Social data
                'social_status' => Arr::random($socialStatuses),
                'father_job' => Arr::random(['مهندس', 'موظف حكومي', 'تاجر', 'مدرس', 'عامل حر']),
                'father_job_type' => Arr::random($fatherJobTypes),
                'mother_job' => Arr::random(['مدرسة', 'ربة بيت', 'موظفة', 'طبيبة']),
                'mother_job_type' => Arr::random($motherJobTypes),

                // Health
                'health_status' => Arr::random($healthStatuses),
                'disease_type' => fake()->boolean(15) ? 'الربو' : null,

                // Transparency
                'has_relatives_at_madareg_administration' => fake()->boolean(20),
                'relatives_at_madareg_administration' => fake()->boolean(20) ? 'قريبة من الإدارة' : null,
                'has_relatives_at_madareg' => fake()->boolean(25),
                'relatives_at_madareg' => fake()->boolean(25) ? 'ابن عم أحد الأعضاء' : null,

                // Contact
                'father_phone' => fake()->phoneNumber(),
                'mother_phone' => fake()->phoneNumber(),

                // Misc
                'active' => true,
                'locked' => false,
                'image_path' => null,
            ]);
        }

        $this->command->info('✅ Subscribers seeded for all member users successfully!');

        $templateAId = FollowUpTemplate::where('code', 'template_a')->value('id');
        $templateBId = FollowUpTemplate::where('code', 'template_b')->value('id');

        if (!$templateAId || !$templateBId) {
            $this->command->warn('⚠️ Follow-up templates not found. Please seed follow_up_templates first.');
            return;
        }

        // Get user IDs that belong to groups 1,2,5
        $userIdsForTemplateB = \Illuminate\Support\Facades\DB::table('group_user')
            ->whereIn('group_id', [1, 2, 5])
            ->pluck('user_id')
            ->unique()
            ->values()
            ->toArray();

        // Users in groups (1,2,5) => Template B
        Subscriber::whereIn('user_id', $userIdsForTemplateB)
            ->update(['follow_up_template_id' => $templateBId]);

        // Others => Template A
        Subscriber::whereNotIn('user_id', $userIdsForTemplateB)
            ->update(['follow_up_template_id' => $templateAId]);

        $this->command->info('✅ follow_up_template_id assigned to subscribers based on user groups (1,2,5).');


    }
}
