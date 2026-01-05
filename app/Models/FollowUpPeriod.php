<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class FollowUpPeriod extends Model
{
    /** @use HasFactory<\Database\Factories\FollowUpPeriodFactory> */
    use HasFactory;

    protected $fillable = [
        'follow_up_template_id',
        'subscriber_id',
        'user_id',
        'year',
        'month',
        'is_month_locked',
        'month_locked_at',
        'month_locked_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'is_month_locked' => 'boolean',
        'month_locked_at' => 'datetime',
    ];

    // Relationships
    public function template(): BelongsTo
    {
        return $this->belongsTo(FollowUpTemplate::class, 'follow_up_template_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(FollowUpEntry::class);
    }

    public function weekLocks(): HasMany
    {
        return $this->hasMany(FollowUpPeriodWeekLock::class);
    }

    public function monthLockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'month_locked_by');
    }

    // Helpers (VERY useful for Filament page)
    public function startDate(): Carbon
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->startOfDay();
    }

    public function endDate(): Carbon
    {
        return $this->startDate()->copy()->endOfMonth()->startOfDay();
    }

    public function daysInMonth(): int
    {
        return $this->startDate()->daysInMonth;
    }

    /**
     * Week index by day of month using 1-7, 8-14, ... like your sheet
     */
    public function weekIndexForDay(int $day): int
    {
        return (int) ceil($day / 7);
    }

    public function isWeekLocked(int $weekIndex): bool
    {
        if ($this->is_month_locked) {
            return true;
        }

        return (bool) $this->weekLocks()
            ->where('week_index', $weekIndex)
            ->value('is_locked');
    }

    public function isDateLocked(Carbon $date): bool
    {
        if ($this->is_month_locked) {
            return true;
        }

        $day = $date->day;
        $weekIndex = $this->weekIndexForDay($day);

        return $this->isWeekLocked($weekIndex);
    }





public function requiredCount(): int
{
    $items = FollowUpItem::where('follow_up_template_id', $this->follow_up_template_id)
        ->where('is_active', 1)
        ->get();

    $dailyCount = $items->where('frequency', 1)->count();
    $weeklyCount = $items->where('frequency', 2)->count();
    $monthlyCount = $items->where('frequency', 3)->count();

    $days = Carbon::createFromDate($this->year, $this->month, 1)->daysInMonth;

    return ($dailyCount * $days) + ($weeklyCount * 5) + ($monthlyCount);
}

public function doneCount(): int
{
    return $this->entries()->where('value', 1)->count();
}

public function completionPercent(): float
{
    $required = $this->requiredCount();
    if ($required === 0) return 0;

    return round(($this->doneCount() / $required) * 100, 1);
}
}
