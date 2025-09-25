<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    protected $table = 'teacher_assessments';
    
    protected $fillable = [
        'title',
        'description',
        'category',
        'number_of_questions',
        'time_limit',
        'difficulty',
        'status',
        'is_live_quiz',
        'available_from',
        'available_until',
        'created_by'
    ];

    protected $casts = [
        'is_live_quiz' => 'boolean',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssessmentAssignment::class);
    }
}
