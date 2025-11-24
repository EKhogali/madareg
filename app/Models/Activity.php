<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityFactory> */
    use HasFactory;
    protected $fillable = ['stage_topic_id', 'title', 'from_date', 'to_date', 'description', 'category'];

    public function details()
    {
        return $this->hasMany(ActivityDetail::class);
    }

    public function stageTopic()
    {
        return $this->belongsTo(StageTopic::class);
    }

    // // Activity.php
    // public function supervisorDetails()
    // {
    //     return $this->hasMany(SupervisorActivityDetail::class);
    // }

    public function supervisorActivityDetails()
{
    return $this->hasMany(\App\Models\SupervisorActivityDetail::class);
}




}
