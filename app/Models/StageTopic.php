<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StageTopic extends Model
{
    /** @use HasFactory<\Database\Factories\StageTopicFactory> */
    use HasFactory;

    protected $fillable = ['stage_id', 'category', 'title'];

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

}
