<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAssignment extends Model
{
    protected $table = 'teacher_assessment_assignments';
    
    protected $fillable = [
        'assessment_id',
        'student_id',
        'section',
        'accommodations',
        'status',
        'assigned_at',
        'due_date'
    ];

    protected $casts = [
        'accommodations' => 'boolean',
        'assigned_at' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
