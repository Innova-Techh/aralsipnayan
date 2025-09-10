<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $table = 'student_profile'; // explicit table name since it's not plural

    protected $fillable = [
        'user_id',
        'firstname',
        'lastname',
        'section',
        'grade_level',
        'school_name',
        'avatar_url',
        'has_completed_onboarding',
        'onboarding_completed_at',
    ];

    protected $casts = [
        'has_completed_onboarding' => 'boolean',
        'onboarding_completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}