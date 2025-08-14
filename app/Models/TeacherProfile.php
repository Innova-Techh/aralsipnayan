<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    protected $table = 'teacher_profile'; // Matches your SQL table name

    protected $fillable = [
        'user_id',
        'firstname',
        'lastname',
        'grade_level_focus',
        'school_name',
        'profile_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
