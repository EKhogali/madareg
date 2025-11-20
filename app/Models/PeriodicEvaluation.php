<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodicEvaluation extends Model
{
    /** @use HasFactory<\Database\Factories\PeriodicEvaluationFactory> */
    use HasFactory;
    protected $fillable = ['title', 'from_date', 'to_date', 'description', 'is_closed'];

    public function details()
    {
        return $this->hasMany(PeriodicEvaluationDetail::class, 'periodic_evaluation_id');
    }
}
