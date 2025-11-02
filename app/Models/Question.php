<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';
    protected $primaryKey = 'question_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'question_id',
        'competency',
        'difficulty_level',
        'topic_tag',
        'question_text',
        'question_type',
        'choice_a',
        'choice_b',
        'choice_c',
        'choice_d',
        'correct_answer',
        'hint_text',
        'explanation',
        'max_allowed_time',
        'estimated_difficulty_weight',
        'question_source',
        'is_active',
        'usage_count',
        'success_rate',
        'base_points',
        'created_by',
    ];
}


