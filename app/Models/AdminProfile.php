<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminProfile extends Model
{
    protected $table = 'admin_profile'; // Explicit because your table name is singular

    protected $fillable = [
        'user_id',
        'firstname',
        'lastname',
        'grade_level_focus',
        'school_name',
        'profile_url',
        'password',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
