<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'last_login_date',
        'status',
        'xp', // Add XP field for level-up system
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login_date' => 'datetime',
        'password' => 'hashed',
        'xp' => 'integer', // Ensure XP is cast as integer
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function teacherProfile()
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function adminProfile()
    {
        return $this->hasOne(AdminProfile::class);
    }

    public function studentMastery()
    {
        return $this->hasMany(StudentMastery::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */
    public function isStudent(): bool
    {
        return $this->role === 'Student';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'Teacher';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    /*
    |--------------------------------------------------------------------------
    | Level System Helpers
    |--------------------------------------------------------------------------
    */
    
    /**
     * Add XP to the user
     */
    public function addXP(int $amount): self
    {
        $this->increment('xp', $amount);
        return $this;
    }

    /**
     * Get current level based on XP
     */
    public function getCurrentLevel(): int
    {
        $levelUpController = new \App\Http\Controllers\LevelUpController();
        return $levelUpController->calculateLevel($this->xp ?? 0);
    }

    /**
     * Get progress information for the user
     */
    public function getProgressInfo(): array
    {
        $levelUpController = new \App\Http\Controllers\LevelUpController();
        return $levelUpController->getProgressInfo($this->xp ?? 0);
    }

    /**
     * Get XP attribute with default value
     */
    public function getXpAttribute($value)
    {
        return $value ?? 0;
    }
}