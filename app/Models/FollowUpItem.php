<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUpItem extends Model
{
    /** @use HasFactory<\Database\Factories\FollowUpItemFactory> */
    use HasFactory;

    protected $fillable = [
        'follow_up_template_id',
        'name_ar',
        'group_ar',
        'frequency',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'frequency' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(FollowUpTemplate::class, 'follow_up_template_id');
    }
}
