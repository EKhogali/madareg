<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUpPeriodWeekLock extends Model
{
    protected $fillable = [
        'follow_up_period_id',
        'week_index',
        'is_locked',
        'locked_at',
        'locked_by',
    ];

    protected $casts = [
        'week_index' => 'integer',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(FollowUpPeriod::class, 'follow_up_period_id');
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
}
