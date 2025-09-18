<?php
// app/Models/StudentProfile.php (continued)

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class StudentProfile extends Model
{
    protected $table = 'student_profile';

    protected $fillable = [
        'user_id',
        'student_id',
        'firstname',
        'lastname',
        'middlename',
        'section',
        'grade_level',
        'school_name',
        'school_year',
        'avatar_url',
        'has_completed_onboarding',
        'onboarding_completed_at',
        'is_first_login',
        'has_viewed_assessments',
        'first_assessment_view_at',
        'current_streak',
        'longest_streak',
        'last_activity_date',
        'total_points',
    ];

    protected $casts = [
        'has_completed_onboarding' => 'boolean',
        'is_first_login' => 'boolean',
        'has_viewed_assessments' => 'boolean',
        'onboarding_completed_at' => 'datetime',
        'first_assessment_view_at' => 'datetime',
        'last_activity_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPointsForDay($day)
    {
        // Points system: Day 1 = 10 points, then +5 points each day
        return 10 + (($day - 1) * 5);
    }

    public function shouldShowModal()
    {
        if (!$this->last_activity_date) {
            return true; // First time login
        }

        $today = Carbon::today();
        return !$this->last_activity_date->isSameDay($today);
    }

    public function updateStreak()
    {
        $today = Carbon::today();
        
        if (!$this->last_activity_date) {
            // First login ever
            $this->current_streak = 1;
            $this->longest_streak = 1;
            $pointsEarned = $this->getPointsForDay(1);
        } else {
            $yesterday = Carbon::yesterday();
            
            if ($this->last_activity_date->isSameDay($yesterday)) {
                // Consecutive day login
                $this->current_streak += 1;
                $pointsEarned = $this->getPointsForDay($this->current_streak);
            } else if ($this->last_activity_date->isSameDay($today)) {
                // Already logged in today
                return ['already_logged' => true, 'points_earned' => 0];
            } else {
                // Streak broken
                $this->current_streak = 1;
                $pointsEarned = $this->getPointsForDay(1);
            }
            
            if ($this->current_streak > $this->longest_streak) {
                $this->longest_streak = $this->current_streak;
            }
        }

        $this->total_points += $pointsEarned;
        $this->last_activity_date = $today;
        $this->save();

        return [
            'already_logged' => false,
            'points_earned' => $pointsEarned,
            'current_streak' => $this->current_streak,
            'total_points' => $this->total_points
        ];
    }

    public function getFullNameAttribute()
    {
        $name = $this->firstname . ' ';
        if ($this->middlename) {
            $name .= $this->middlename . ' ';
        }
        $name .= $this->lastname;
        return $name;
    }
}