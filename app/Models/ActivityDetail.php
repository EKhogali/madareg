<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityDetail extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityDetailFactory> */
    use HasFactory;
    
    protected $fillable = ['activity_id', 'subscriber_id', 'evaluation', 'notes'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
