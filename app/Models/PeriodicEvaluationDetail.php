<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodicEvaluationDetail extends Model
{
    /** @use HasFactory<\Database\Factories\PeriodicEvaluationDetailFactory> */
    use HasFactory;

    protected $fillable = ['periodic_evaluation_id', 'subscriber_id', 'evaluation_area', 'evaluation_score', 'notes'];

    public function periodicEvaluation()
    {
        return $this->belongsTo(PeriodicEvaluation::class, 'periodic_evaluation_id');
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
