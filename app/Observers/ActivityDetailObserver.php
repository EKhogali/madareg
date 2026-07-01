<?php

namespace App\Observers;

use App\Models\ActivityDetail;
use App\Models\Subscriber;
use App\Models\Stage;
use Illuminate\Support\Facades\DB;

class ActivityDetailObserver
{
    public function created(ActivityDetail $activityDetail): void
    {
        $this->recalculate($activityDetail->subscriber);
    }

    public function updated(ActivityDetail $activityDetail): void
    {
        $this->recalculate($activityDetail->subscriber);
    }

    public function deleted(ActivityDetail $activityDetail): void
    {
        $this->recalculate($activityDetail->subscriber);
    }

    // ─────────────────────────────────────────
    // Core: recalculate track_degree_id from scratch
    // ─────────────────────────────────────────
    private function recalculate(Subscriber $subscriber): void
    {
        // 1. Sum all evaluations for this subscriber
        $total = (int) ActivityDetail::where('subscriber_id', $subscriber->id)
            ->sum('evaluation');

        // 2. Clamp to 1000 max
        $points = min($total, 1000);

        // 3. total_points = raw score (new dedicated column)
        $subscriber->total_points = $points;

        // 4. track_degree_id = FK to track_degrees (ID matches point value)
        //    If points = 0, set null (no degree yet)
        $subscriber->track_degree_id = $points > 0 ? $points : null;

        // 5. Save both at once
        $subscriber->save();

        // 6. Check stage promotion (unchanged)
        $this->checkStageCompletionAndPromote($subscriber);
    }

    // ─────────────────────────────────────────
    // Stage promotion (unchanged from before)
    // ─────────────────────────────────────────
    private function checkStageCompletionAndPromote(Subscriber $subscriber): void
    {
        $counts = DB::table('activity_details AS ad')
            ->join('activities AS a', 'a.id', '=', 'ad.activity_id')
            ->join('stage_topics AS st', 'st.id', '=', 'a.stage_topic_id')
            ->select('st.category', DB::raw('COUNT(DISTINCT a.stage_topic_id) as topic_count'))
            ->where('ad.subscriber_id', $subscriber->id)
            ->whereNotNull('a.stage_topic_id')
            ->groupBy('st.category')
            ->pluck('topic_count', 'st.category');

        $hasFullPackage =
            ($counts['مهارات'] ?? 0) >= 2 &&
            ($counts['قيم'] ?? 0) >= 2 &&
            ($counts['معارف'] ?? 0) >= 2 &&
            ($counts['المخيم'] ?? 0) >= 1;

        if ($hasFullPackage) {
            $nextStage = Stage::where('id', '>', $subscriber->stage_id ?? 0)
                ->orderBy('id')
                ->first();

            if ($nextStage) {
                $subscriber->stage_id = $nextStage->id;
                $subscriber->save();
            }
        }
    }
}