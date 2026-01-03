<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUpEntry extends Model
{
    protected $fillable = [
        'follow_up_period_id',
        'follow_up_item_id',
        'date',
        'week_index',
        'value',
    ];

    protected $casts = [
        'date' => 'date',
        'week_index' => 'integer',
        'value' => 'integer', // null allowed
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(FollowUpPeriod::class, 'follow_up_period_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(FollowUpItem::class, 'follow_up_item_id');
    }

    public function isDone(): bool
    {
        return $this->value === 1;
    }

    public function isNotDone(): bool
    {
        return $this->value === 0;
    }

    public function isUnknown(): bool
    {
        return $this->value === null;
    }
}
