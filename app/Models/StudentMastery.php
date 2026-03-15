<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentMastery extends Model
{
    protected $table = 'student_mastery';

    protected $primaryKey = 'mastery_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'mastery_id',
        'user_id',
        'competency',
        'current_difficulty',
        'accuracy_score',
        'accuracy_component',
        'bkt_score',
        'bkt_component',
        'final_mastery_score',
        'prior_knowledge',
        'learn_rate',
        'slip_rate',
        'guess_rate',
        'has_taken_diagnostic',
        'diagnostic_completed_at',
        'total_questions_answered',
        'correct_answers',
        'total_assessments_taken',
        'cumulative_time_score',
        'current_ftime_factor',
        'average_time_factor',
    ];

    protected $casts = [
        'accuracy_score' => 'float',
        'accuracy_component' => 'float',
        'bkt_score' => 'float',
        'bkt_component' => 'float',
        'final_mastery_score' => 'float',
        'prior_knowledge' => 'float',
        'learn_rate' => 'float',
        'slip_rate' => 'float',
        'guess_rate' => 'float',
        'has_taken_diagnostic' => 'boolean',
        'diagnostic_completed_at' => 'datetime',
        'total_questions_answered' => 'integer',
        'correct_answers' => 'integer',
        'total_assessments_taken' => 'integer',
        'cumulative_time_score' => 'float',
        'current_ftime_factor' => 'float',
        'average_time_factor' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
