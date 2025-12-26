<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RadmDetection extends Model
{
    protected $fillable = [
        'session_id',
        'student_id',
        'assessment_id',
        'window_start_index',
        'window_end_index',
        'question_ids',
        'avg_response_time',
        'expected_time',
        'time_threshold',
        'time_flag',
        'correct_count',
        'total_count',
        'accuracy',
        'accuracy_flag',
        'bkt_probability',
        'consecutive_wrong',
        'bkt_flag',
        'rai_score',
        'intervention_triggered',
        'intervention_acknowledged',
        'acknowledged_at',
        'difficulty_level',
        'response_times',
        'correctness',
    ];

    protected $casts = [
        'question_ids' => 'array',
        'time_flag' => 'boolean',
        'accuracy_flag' => 'boolean',
        'bkt_flag' => 'boolean',
        'intervention_triggered' => 'boolean',
        'intervention_acknowledged' => 'boolean',
        'response_times' => 'array',
        'correctness' => 'array',
        'acknowledged_at' => 'datetime',
        'avg_response_time' => 'decimal:2',
        'expected_time' => 'decimal:2',
        'time_threshold' => 'decimal:2',
        'accuracy' => 'decimal:4',
        'bkt_probability' => 'decimal:4',
        'rai_score' => 'decimal:4',
    ];

    /**
     * Get the student associated with this detection
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the assessment session
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(AssessmentSession::class, 'session_id', 'session_id');
    }

    /**
     * Get the assessment
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id', 'assessment_id');
    }

    /**
     * Scope to get only triggered interventions
     */
    public function scopeTriggered($query)
    {
        return $query->where('intervention_triggered', true);
    }

    /**
     * Scope to get unacknowledged interventions
     */
    public function scopeUnacknowledged($query)
    {
        return $query->where('intervention_triggered', true)
                     ->where('intervention_acknowledged', false);
    }

    /**
     * Scope for a specific session
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope for a specific student
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Mark intervention as acknowledged
     */
    public function acknowledge(): void
    {
        $this->update([
            'intervention_acknowledged' => true,
            'acknowledged_at' => now(),
        ]);
    }
}
