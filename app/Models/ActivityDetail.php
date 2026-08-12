<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityDetail extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityDetailFactory> */
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'subscriber_id',
        'att_present',
        'att_on_time',
        'att_interaction',
        'att_subscription',
        'evaluation',
        'notes',
    ];

    protected $casts = [
        'att_present' => 'boolean',
        'att_on_time' => 'boolean',
        'att_interaction' => 'boolean',
        'att_subscription' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $detail) {
            $detail->evaluation =
                ($detail->att_present ? 5 : 0) +
                ($detail->att_on_time ? 5 : 0) +
                ($detail->att_interaction ? 5 : 0) +
                ($detail->att_subscription ? 5 : 0);
        });
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
