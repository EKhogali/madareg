<?php

namespace App\Support\Traits;

use App\Models\Subscriber;

trait HasSupervisorScope
{
    protected function supervisorGroupIds(): array
    {
        $u = auth()->user();

        if (! $u) {
            return [];
        }

        return $u->groups()
            ->pluck('groups.id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    protected function scopeSubscribersForCurrentUser()
    {
        $u = auth()->user();

        // No user => empty query
        if (! $u) {
            return Subscriber::query()->whereRaw('1=0');
        }

        // Supervisor => subscribers in supervisor groups
        if ((int) $u->role === 3) {
            return Subscriber::query()->whereIn('group_id', $this->supervisorGroupIds());
        }

        // Others (admin/monitor/superadmin) => all subscribers
        return Subscriber::query();
    }

    protected function assertSubscriberVisibleToCurrentUser(int $subscriberId): void
    {
        $u = auth()->user();

        if (! $u) {
            abort(403);
        }

        if ((int) $u->role === 3) {
            $ok = Subscriber::query()
                ->where('id', $subscriberId)
                ->whereIn('group_id', $this->supervisorGroupIds())
                ->exists();

            abort_unless($ok, 403);
        }
    }
}
