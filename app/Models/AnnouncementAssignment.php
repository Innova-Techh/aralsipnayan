<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementAssignment extends Model
{
    protected $table = 'teacher_announcement_assignments';

    protected $fillable = [
        'announcement_id',
        'student_id',
        'section'
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class, 'announcement_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
