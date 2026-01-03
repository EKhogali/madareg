<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FollowUpTemplate;
use App\Models\FollowUpItem;

class FollowUpTemplateASeeder extends Seeder
{
    public function run(): void
    {
        // Template A
        $template = FollowUpTemplate::updateOrCreate(
            ['code' => 'template_a'],
            ['name_ar' => 'نموذج المتابعة - فئة A', 'is_active' => true]
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

        // الصلوات الخمس
        foreach (['الفجر', 'الظهر', 'العصر', 'المغرب', 'العشاء'] as $name) {
            $items[] = [
                'follow_up_template_id' => $template->id,
                'name_ar' => $name,
                'group_ar' => 'الصلوات الخمس',
                'frequency' => $DAILY,
                'sort_order' => $order++,
                'is_active' => true,
            ];
        }

        // صلاة النوافل
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

        // الأذكار
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

        // أخرى
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

        // Upsert (avoid duplicates if you re-run)
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
