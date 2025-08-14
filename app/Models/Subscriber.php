<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriberFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'birth_date',
        'birth_place',
        'residence_place',
        'nationality',
        'study_level',
        'education_type',
        'school_name',
        'is_quran_student',
        'quran_amount',
        'quran_memorization_center',
        'talents',
        'social_status',
        'father_job',
        'father_job_type',
        'mother_job',
        'mother_job_type',
        'health_status',
        'disease_type',
        'has_relatives_at_madareg_administration',
        'relatives_at_madareg_administration',
        'has_relatives_at_madareg',
        'relatives_at_madareg',
        'father_phone',
        'mother_phone',
        'image_path',
    ];
}
