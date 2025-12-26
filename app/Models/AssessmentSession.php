<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentSession extends Model
{
    protected $primaryKey = 'session_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'session_id',
        'user_id',
        'assessment_id',
        'session_type',
        'competency',
        'difficulty_level',
        'total_questions',
        'questions_json',
        'current_question_index',
        'time_limit_minutes',
        'total_time_allowed_seconds',
        'countdown_started_at',
        'countdown_expires_at',
        'time_remaining_seconds',
        'auto_submit_on_timeout',
        'beginner_question_time_limit',
        'intermediate_question_time_limit',
        'advanced_question_time_limit',
        'current_question_time_limit',
        'paused_at',
        'total_paused_time_seconds',
        'is_paused',
        'questions_answered',
        'correct_answers',
        'incorrect_answers',
        'total_points_earned',
        'accuracy_percentage',
        'accuracy_component',
        'average_response_time',
        'time_performance_score',
        'cumulative_time_score',
        'initial_bkt_probability',
        'final_bkt_probability',
        'bkt_component',
        'final_mastery_score',
        'is_adaptive',
        'difficulty_progression_factor',
        'status',
        'started_at',
        'completed_at',
        'last_activity_at',
    ];

    protected $casts = [
        'questions_json' => 'array',
        'auto_submit_on_timeout' => 'boolean',
        'is_paused' => 'boolean',
        'is_adaptive' => 'boolean',
        'countdown_started_at' => 'datetime',
        'countdown_expires_at' => 'datetime',
        'paused_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Get the user for this session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the assessment
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id', 'assessment_id');
    }

    /**
     * Get question responses for this session
     */
    public function questionResponses(): HasMany
    {
        return $this->hasMany(QuestionResponse::class, 'assessment_id', 'assessment_id')
                    ->where('user_id', $this->user_id);
    }

    /**
     * Get RADM detections for this session
     */
    public function radmDetections(): HasMany
    {
        return $this->hasMany(RadmDetection::class, 'session_id', 'session_id');
    }
}
