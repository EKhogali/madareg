<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FollowUpTemplate;
use App\Models\FollowUpItem;

class FollowUpTemplateBSeeder extends Seeder
{
    public function run(): void
    {
        // Template B
        $template = FollowUpTemplate::updateOrCreate(
            ['code' => 'template_b'],
            ['name_ar' => 'نموذج المتابعة - فئة B', 'is_active' => true]
        );

        // Frequency: 1 daily, 2 weekly, 3 monthly
        $DAILY = 1;
        $WEEKLY = 2;
        $MONTHLY = 3;

        $order = 1;
        $items = [];

        // =======================
        // الأعمال اليومية
        // =======================

        // ✅ Correct order: جماعة THEN راتبتها
        $prayers = ['الفجر', 'الظهر', 'العصر', 'المغرب', 'العشاء'];

        foreach ($prayers as $p) {

            $items[] = [
                'follow_up_template_id' => $template->id,
                'name_ar' => "{$p} ",
                'group_ar' => 'الصلوات الخمس ورداتها',
                'frequency' => $DAILY,
                'sort_order' => $order++,
                'is_active' => true,
            ];

            $items[] = [
                'follow_up_template_id' => $template->id,
                'name_ar' => "{$p} - راتبتها",
                'group_ar' => 'الصلوات الخمس ورداتها',
                'frequency' => $DAILY,
                'sort_order' => $order++,
                'is_active' => true,
            ];
        }

        // صلاة النوافل (حسب الصورة: الشفع، الوتر، الضحى)
        foreach (['الشفع', 'الوتر', 'الضحى'] as $name) {
            $items[] = [
                'follow_up_template_id' => $template->id,
                'name_ar' => $name,
                'group_ar' => 'صلاة النوافل',
                'frequency' => $DAILY,
                'sort_order' => $order++,
                'is_active' => true,
            ];
        }

        // الأذكار (صباح/مساء)
        foreach (['صباح', 'مساء'] as $name) {
            $items[] = [
                'follow_up_template_id' => $template->id,
                'name_ar' => $name,
                'group_ar' => 'الأذكار',
                'frequency' => $DAILY,
                'sort_order' => $order++,
                'is_active' => true,
            ];
        }

        // أخرى (قرآن/دعاء/المساعدة في المنزل)
        foreach (['قرآن', 'دعاء', 'المساعدة في المنزل'] as $name) {
            $items[] = [
                'follow_up_template_id' => $template->id,
                'name_ar' => $name,
                'group_ar' => 'أخرى',
                'frequency' => $DAILY,
                'sort_order' => $order++,
                'is_active' => true,
            ];
        }

        // =======================
        // أعمال الأسبوع
        // =======================
        foreach (['حفظ حديث', 'صلة رحم', 'صدقة'] as $name) {
            $items[] = [
                'follow_up_template_id' => $template->id,
                'name_ar' => $name,
                'group_ar' => 'أعمال الأسبوع',
                'frequency' => $WEEKLY,
                'sort_order' => $order++,
                'is_active' => true,
            ];
        }

        // =======================
        // أعمال الشهر
        // =======================
        foreach (['الإحسان للجيران', 'قراءة كتاب'] as $name) {
            $items[] = [
                'follow_up_template_id' => $template->id,
                'name_ar' => $name,
                'group_ar' => 'أعمال الشهر',
                'frequency' => $MONTHLY,
                'sort_order' => $order++,
                'is_active' => true,
            ];
        }

        // ✅ Upsert
        foreach ($items as $row) {
            FollowUpItem::updateOrCreate(
                [
                    'follow_up_template_id' => $row['follow_up_template_id'],
                    'name_ar' => $row['name_ar'],
                    'frequency' => $row['frequency'],
                ],
                $row
            );
        }
    }
}
