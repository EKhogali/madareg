<?php

namespace App\Observers;

use App\Models\SupervisorActivityDetail;
use App\Models\User;

class SupervisorActivityDetailObserver
{
    public function created(SupervisorActivityDetail $detail): void
    {
        $this->recalculate($detail->supervisor_id);
    }

    public function updated(SupervisorActivityDetail $detail): void
    {
        $this->recalculate($detail->supervisor_id);
    }

    public function deleted(SupervisorActivityDetail $detail): void
    {
        $this->recalculate($detail->supervisor_id);
    }

    // ─────────────────────────────────────────
    // Recalculate total_points from scratch
    // ─────────────────────────────────────────
    private function recalculate(int $supervisorId): void
    {
        $total = (int) SupervisorActivityDetail::where('supervisor_id', $supervisorId)
            ->sum('evaluation');

        $points = min($total, 1000);

        User::where('id', $supervisorId)
            ->update(['total_points' => $points]);
    }
}