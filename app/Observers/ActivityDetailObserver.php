<?php

namespace App\Observers;

use App\Models\ActivityDetail;
use App\Models\Subscriber;
use App\Models\Stage;
use Illuminate\Support\Facades\DB;

class ActivityDetailObserver
{
    public function created(ActivityDetail $activityDetail)
    {
        $subscriber = $activityDetail->subscriber;

        if ($this->hasNewlyCompletedPackage($subscriber)) {
            $this->promoteSubscriber($subscriber);
        }
    }

    private function hasNewlyCompletedPackage(Subscriber $subscriber): bool
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

        return $hasFullPackage;
    }

    private function promoteSubscriber(Subscriber $subscriber): void
    {
        $nextStage = Stage::where('id', '>', $subscriber->stage_id)
            ->orderBy('id')
            ->first();

        if ($nextStage) {
            $subscriber->stage_id = $nextStage->id;
            $subscriber->save();
        }
    }


    private function calculateNewTrackDegree(Subscriber $subscriber, int $points): int
    {
        // Sample implementation: you must define how degrees are determined
        $currentDegreeId = $subscriber->track_degree_id ?? 0;
        return $currentDegreeId + $points;
    }

    // private function checkStageCompletionAndPromote(Subscriber $subscriber)
    // {
    //     $stageId = $subscriber->stage_id;

    //     $completed = ActivityDetail::query()
    //         ->where('subscriber_id', $subscriber->id)
    //         ->whereHas('activity.stageTopic', function ($query) use ($stageId) {
    //             $query->where('stage_id', $stageId);
    //         })
    //         ->join('activities', 'activity_details.activity_id', '=', 'activities.id')
    //         ->selectRaw('activities.stage_topic_id, COUNT(*) as count')
    //         ->groupBy('activities.stage_topic_id')
    //         ->pluck('count', 'activities.stage_topic_id');

    //     // Now you can use $completed to evaluate stage progress
    // }


    private function checkStageCompletionAndPromote(Subscriber $subscriber)
    {
        $counts = DB::table('activity_details AS ad')
            ->join('activities AS a', 'a.id', '=', 'ad.activity_id')
            ->join('stage_topics AS st', 'st.id', '=', 'a.stage_topic_id')
            ->select('st.category', DB::raw('COUNT(DISTINCT a.stage_topic_id) as topic_count'))
            ->where('ad.subscriber_id', $subscriber->id)
            ->groupBy('st.category')
            ->pluck('topic_count', 'st.category');

        $hasFullPackage =
            ($counts['مهارات'] ?? 0) >= 2 &&
            ($counts['قيم'] ?? 0) >= 2 &&
            ($counts['معارف'] ?? 0) >= 2 &&
            ($counts['المخيم'] ?? 0) >= 1;

        if ($hasFullPackage) {
            $currentStageId = $subscriber->stage_id ?? 0;

            $nextStage = Stage::where('id', '>', $currentStageId)->orderBy('id')->first();

            if ($nextStage) {
                $subscriber->stage_id = $nextStage->id;
                $subscriber->save();
            }
        }
    }

}
