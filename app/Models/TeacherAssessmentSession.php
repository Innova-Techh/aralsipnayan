<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherAssessmentSession extends Model
{
    protected $primaryKey = 'session_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'session_id',
        'user_id',
        'teacher_assessment_id',
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
        'status',
        'started_at',
        'completed_at',
        'last_activity_at',
        'session_notes',
        'session_metadata'
    ];

    protected $casts = [
        'questions_json' => 'array',
        'session_metadata' => 'array',
        'countdown_started_at' => 'datetime',
        'countdown_expires_at' => 'datetime',
        'paused_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'auto_submit_on_timeout' => 'boolean',
        'is_paused' => 'boolean',
    ];

    /**
     * Get the user (student) who owns this session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the teacher assessment this session belongs to
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'teacher_assessment_id');
    }

    /**
     * Get all quiz responses for this session
     */
    public function responses(): HasMany
    {
        return $this->hasMany(TeacherQuizResponse::class, 'session_id', 'session_id');
    }

    /**
     * Calculate and update BKT probability
     */
    public function updateBKTProbability($isCorrect, $currentProbability = null)
    {
        // BKT parameters (can be adjusted based on your model)
        $pInit = $currentProbability ?? $this->initial_bkt_probability ?? 0.5; // Initial knowledge
        $pLearn = 0.3; // Probability of learning
        $pSlip = 0.1; // Probability of slip (know but answer wrong)
        $pGuess = 0.25; // Probability of guess (don't know but answer right)

        if ($isCorrect) {
            // Update probability given correct answer
            $numerator = $pInit * (1 - $pSlip);
            $denominator = $pInit * (1 - $pSlip) + (1 - $pInit) * $pGuess;
            $pKnowledge = $numerator / $denominator;
        } else {
            // Update probability given incorrect answer
            $numerator = $pInit * $pSlip;
            $denominator = $pInit * $pSlip + (1 - $pInit) * (1 - $pGuess);
            $pKnowledge = $numerator / $denominator;
        }

        // Apply learning
        $newProbability = $pKnowledge + (1 - $pKnowledge) * $pLearn;

        return round($newProbability, 4);
    }

    /**
     * Calculate final mastery score
     */
    public function calculateFinalMasteryScore()
    {
        $accuracyWeight = 0.6;
        $bktWeight = 0.4;

        $accuracyScore = $this->accuracy_percentage / 100;
        $bktScore = $this->final_bkt_probability ?? 0.5;

        return round(($accuracyScore * $accuracyWeight + $bktScore * $bktWeight) * 100, 2);
    }
}
