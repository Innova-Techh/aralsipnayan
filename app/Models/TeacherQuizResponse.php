<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherQuizResponse extends Model
{
    protected $fillable = [
        'session_id',
        'pool_id',
        'question_id',
        'student_id',
        'student_answer',
        'correct_answer',
        'is_correct',
        'points_earned',
        'time_taken',
        'bkt_probability_before',
        'bkt_probability_after',
        'difficulty_level',
        'competency',
        'topic_tag',
        'answered_at'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
    ];

    /**
     * Get the session this response belongs to
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(TeacherAssessmentSession::class, 'session_id', 'session_id');
    }

    /**
     * Get the student who submitted this response
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Calculate points earned based on correctness and time taken
     */
    public function calculatePoints($isCorrect, $timeTaken, $timeLimit, $difficultyLevel)
    {
        if (!$isCorrect) {
            return 0;
        }

        // Base points by difficulty
        $basePoints = [
            'beginner' => 10,
            'intermediate' => 15,
            'advanced' => 20
        ];

        $points = $basePoints[$difficultyLevel] ?? 10;

        // Time bonus (up to 50% extra for quick answers)
        $timeRatio = $timeTaken / $timeLimit;
        if ($timeRatio < 0.5) {
            $timeBonus = $points * 0.5; // 50% bonus
        } elseif ($timeRatio < 0.75) {
            $timeBonus = $points * 0.25; // 25% bonus
        } else {
            $timeBonus = 0;
        }

        return round($points + $timeBonus);
    }
}
