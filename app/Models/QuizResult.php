<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizResult extends Model
{
    protected $fillable = [
        'student_id',
        'assessment_id',
        'session_id',
        'score',
        'total_questions',
        'percentage',
        'time_taken',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(TeacherAssessmentSession::class, 'session_id', 'session_id');
    }
}
