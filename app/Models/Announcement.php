<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'teacher_announcements';

    protected $fillable = [
        'teacher_id',
        'title',
        'content',
        'priority'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function assignments()
    {
        return $this->hasMany(AnnouncementAssignment::class, 'announcement_id');
    }
}
