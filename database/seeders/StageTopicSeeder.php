<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Stage;
use App\Models\StageTopic;

class StageTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            'بصيص' => [
                'معارف' => ['الأسماء الحسنى', 'السيرة النبوية'],
                'قيم'   => ['الصدق', 'الأمانة'],
                'مهارات' => ['الاحترام', 'التعاون'],
            ],
            'بريق' => [
                'معارف' => ['أركان الإيمان', 'الطهارة والصلاة'],
                'قيم'   => ['الانتماء', 'النظام'],
                'مهارات' => ['إدارة الوقت', 'التخطيط الشخصي'],
            ],
            'وميض' => [
                'معارف' => ['الخلافة الراشدة', 'الزكاة والصوم والحج'],
                'قيم'   => ['العطاء', 'المسؤولية'],
                'مهارات' => ['الحوار', 'حل المشكلات'],
            ],
            'ضياء' => [
                'معارف' => ['تاريخ ليبيا', 'أحكام الأسرة'],
                'قيم'   => ['المبادرة', 'المرونة'],
                'مهارات' => ['التفكير الإبداعي', 'القيادة الأخلاقية'],
            ],
            'نور' => [
                'معارف' => ['أطلس العالم', 'أحكام البيع'],
                'قيم'   => ['الحرية', 'الإنجاز'],
                'مهارات' => ['معالجة المعلومات', 'رعاية التقدم'],
            ],
        ];

        foreach ($topics as $stageName => $categories) {
            $stage = Stage::where('name', $stageName)->first();

            if (!$stage) {
                continue;
            }

            foreach ($categories as $category => $topicList) {
                foreach ($topicList as $topic) {
                    StageTopic::create([
                        'stage_id' => $stage->id,
                        'category' => $category,
                        'name' => $topic,
                    ]);
                }
                
            }
            
            StageTopic::create([
                        'stage_id' => $stage->id,
                        'category' => 'المخيم',
                        'name' => 'مخيم المدارج',
                    ]);
        }
    }
}
