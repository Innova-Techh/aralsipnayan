<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAssessmentQuestion extends Model
{
    protected $table = 'teacher_assessment_questions';
    protected $primaryKey = 'pool_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'pool_id',
        'teacher_assessment_id',
        'question_id',
        'question_order',
        'is_answered',
        'is_current',
        'created_by',
        'added_at'
    ];

    protected $casts = [
        'is_answered' => 'boolean',
        'is_current' => 'boolean',
        'added_at' => 'datetime',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'teacher_assessment_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
