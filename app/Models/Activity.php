<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityFactory> */
    use HasFactory;
    protected $fillable = ['title', 'from_date', 'to_date', 'description', 'category'];

    public function details()
    {
        return $this->hasMany(ActivityDetail::class);
    }
}
