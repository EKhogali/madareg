<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FollowUpTemplate extends Model
{
    /** @use HasFactory<\Database\Factories\FollowUpTemplateFactory> */
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(FollowUpItem::class);
    }

    public function periods(): HasMany
    {
        return $this->hasMany(FollowUpPeriod::class);
    }
}
