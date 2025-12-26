<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionResponse extends Model
{
    protected $primaryKey = 'response_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'response_id',
        'assessment_id',
        'user_id',
        'question_id',
        'user_answer',
        'is_correct',
        'response_time',
        'max_allowed_time',
        'normalized_time',
        'time_score',
        'bkt_before',
        'bkt_after',
        'difficulty_factor',
        'time_factor',
        'ftime_factor',
        'base_points',
        'time_bonus_points',
        'total_points',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'response_time' => 'decimal:3',
        'normalized_time' => 'decimal:4',
        'time_score' => 'decimal:2',
        'bkt_before' => 'decimal:4',
        'bkt_after' => 'decimal:4',
        'difficulty_factor' => 'decimal:2',
        'time_factor' => 'decimal:4',
        'ftime_factor' => 'decimal:4',
        'answered_at' => 'datetime',
    ];

    /**
     * Get the user who answered this question
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the question
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id', 'question_id');
    }

    /**
     * Get the assessment
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id', 'assessment_id');
    }
}
