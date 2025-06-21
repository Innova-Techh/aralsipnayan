<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable (allowable for mass assignment).
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'last_login_date',
        'status',
    ];

    /**
     * Attributes hidden from JSON serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute type casting.
     */
    protected $casts = [
        'last_login_date' => 'datetime',
        'password' => 'hashed',
    ];
}
