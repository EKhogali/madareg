<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subscriber;
use App\Models\FollowUpTemplate;
use Illuminate\Support\Arr;

class SubscriberSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Fetch all parents (members)
        $parents = User::where('role', User::ROLE_MEMBER)->orderBy('id')->get();

        if ($parents->isEmpty()) {
            $this->command->warn('⚠️ No parent users found. Please seed users first.');
            return;
        }

        // ✅ Templates
        $templateAId = FollowUpTemplate::where('code', 'template_a')->value('id');
        $templateBId = FollowUpTemplate::where('code', 'template_b')->value('id');

        if (!$templateAId || !$templateBId) {
            $this->command->warn('⚠️ Follow-up templates not found. Please seed follow_up_templates first.');
            return;
        }

        // ✅ IDs (adjust if needed)
        $groupIds = [1, 2, 3, 4, 5];
        $stageIds = [1, 2, 3, 4, 5];
        $trackDegreeIds = [1, 2, 3, 4, 5];

        // ✅ Other faker options
        $studyLevels = ['الصف الأول', 'الصف الثاني', 'الصف الثالث', 'الصف الرابع', 'الصف الخامس', 'الصف السادس'];
        $educationTypes = [0, 1, 2];
        $socialStatuses = [0, 1, 2, 3, 4, 5, 6];
        $fatherJobTypes = [0, 1, 2, 3];
        $motherJobTypes = [0, 1, 2, 3];
        $healthStatuses = [0, 1];

        // ✅ EVEN DISTRIBUTION
        // We'll rotate groups, stages, track degrees in the same way.
        $groupCount = count($groupIds);
        $stageCount = count($stageIds);
        $trackCount = count($trackDegreeIds);

        foreach ($parents as $index => $parent) {

            // ✅ Round-robin distribution
            $groupId = $groupIds[$index % $groupCount];
            $stageId = $stageIds[$index % $stageCount];
            $trackDegreeId = $trackDegreeIds[$index % $trackCount];

            // ✅ Template rule based on GROUP
            $templateId = in_array($groupId, [1, 2, 5], true)
                ? $templateBId
                : $templateAId;

            Subscriber::updateOrCreate(
                ['user_id' => $parent->id],
                [
                    'name' => "مشترك - {$parent->name}",

                    'group_id' => $groupId,
                    'stage_id' => $stageId,
                    'track_degree_id' => $trackDegreeId,

                    'follow_up_template_id' => $templateId,

                    'birth_date' => fake()->dateTimeBetween('-18 years', '-8 years'),
                    'birth_place' => fake()->city(),
                    'residence_place' => fake()->city(),
                    'nationality' => 'ليبية',
                    'gender' => Arr::random([1, 2]),

                    'study_level' => Arr::random($studyLevels),
                    'education_type' => Arr::random($educationTypes),
                    'school_name' => 'مدرسة ' . fake()->word(),

                    'is_quran_student' => fake()->boolean(70),
                    'quran_amount' => Arr::random(['جزء', 'جزئين', '5 أجزاء', '10 أجزاء', '15 جزء']),
                    'quran_memorization_center' => fake()->boolean(70) ? 'مركز النور' : null,

                    'talents' => Arr::random(['الخط العربي', 'الرسم', 'الإنشاد', 'التمثيل', 'الرياضة', 'البرمجة']),

                    'social_status' => Arr::random($socialStatuses),
                    'father_job' => Arr::random(['مهندس', 'موظف حكومي', 'تاجر', 'مدرس', 'عامل حر']),
                    'father_job_type' => Arr::random($fatherJobTypes),
                    'mother_job' => Arr::random(['مدرسة', 'ربة بيت', 'موظفة', 'طبيبة']),
                    'mother_job_type' => Arr::random($motherJobTypes),

                    'health_status' => Arr::random($healthStatuses),
                    'disease_type' => fake()->boolean(15) ? 'الربو' : null,

                    'has_relatives_at_madareg_administration' => fake()->boolean(20),
                    'relatives_at_madareg_administration' => fake()->boolean(20) ? 'قريبة من الإدارة' : null,
                    'has_relatives_at_madareg' => fake()->boolean(25),
                    'relatives_at_madareg' => fake()->boolean(25) ? 'ابن عم أحد الأعضاء' : null,

                    'father_phone' => fake()->phoneNumber(),
                    'mother_phone' => fake()->phoneNumber(),

                    'active' => true,
                    'locked' => false,
                    'image_path' => null,
                ]
            );
        }

        $this->command->info("✅ Subscribers seeded successfully with EVEN group/stage/track distribution.");
    }
}
