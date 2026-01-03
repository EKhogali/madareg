<?php
namespace App\Policies;

use App\Models\User;
use App\Models\FollowUpPeriod;

class FollowUpPeriodPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->status === 1;
    }

    public function view(User $user, FollowUpPeriod $period): bool
    {
        return $user->isStaff() || $period->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        // Usually created automatically by the page; allow member too
        return $user->status === 1;
    }

    public function update(User $user, FollowUpPeriod $period): bool
    {
        // Members can update only if not locked; staff can override if you want
        if ($user->isStaff()) {
            return true;
        }

        return $period->user_id === $user->id && !$period->is_month_locked;
    }

    public function lock(User $user, FollowUpPeriod $period): bool
    {
        // Suggestion: staff can lock/unlock, members can lock only their own
        return $user->isStaff() || $period->user_id === $user->id;
    }

    public function unlock(User $user, FollowUpPeriod $period): bool
    {
        // Safer: only staff unlock
        return $user->isStaff();
    }
}
