<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
        'date_range_start',
        'date_range_end',
        'active',
        'color',
    ];

    protected $casts = [
        'active' => 'boolean',
        'date_range_start' => 'date',
        'date_range_end' => 'date',
    ];

public function users()
{
    return $this->belongsToMany(User::class)->withTimestamps();
}

public function supervisors()
{
    return $this->belongsToMany(User::class)
        ->withTimestamps()
        ->where('users.role', 3);
}

public function members()
{
    return $this->belongsToMany(User::class)
        ->withTimestamps()
        ->where('users.role', 4);
}

}
