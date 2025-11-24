<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    public function topics()
    {
        return $this->hasMany(StageTopic::class);
    }
}
