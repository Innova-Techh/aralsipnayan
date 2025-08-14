<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\AchievementController;

// Homepage
Route::get('/', function () {
    return view('homepage');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// General dashboard redirect (based on role)
Route::get('/dashboard', function() {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    switch(Auth::user()->role) {
        case 'Admin':
            return redirect()->route('admin.dashboard');
        case 'Teacher':
            return redirect()->route('teacher.dashboard');
        case 'Student':
            return redirect()->route('student.dashboard');
        default:
            return redirect('/');
    }
})->name('dashboard');

// Student Dashboard
Route::get('/student/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:Student'])
    ->name('student.dashboard');

// Teacher Dashboard (placeholder)
Route::get('/teacher/dashboard', function () {
    return 'Teacher Dashboard (Coming Soon)';
})->middleware(['auth', 'role:Teacher'])->name('teacher.dashboard');

// Admin Dashboard (placeholder)
Route::get('/admin/dashboard', function () {
    return 'Admin Dashboard (Coming Soon)';
})->middleware(['auth', 'role:Admin'])->name('admin.dashboard');

// Dashboard stats (accessible to authenticated users only)
Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])
    ->middleware('auth')
    ->name('dashboard.stats');

// Assessments
Route::get('/assessments', fn() => view('user.assessments'))
    ->middleware('auth')
    ->name('assessments.index');

// Achievements
Route::get('/achievements', [AchievementController::class, 'index'])
    ->middleware('auth')
    ->name('achievements.index');

// Progression
Route::get('/progression', fn() => view('user.progression'))
    ->middleware('auth')
    ->name('progression.index');

// Leaderboard
Route::get('/leaderboard', [LeaderboardController::class, 'index'])
    ->middleware('auth')
    ->name('leaderboard.index');
Route::get('/leaderboard/data', [LeaderboardController::class, 'getLeaderboardData'])
    ->middleware('auth')
    ->name('leaderboard.data');

// Profile edit
Route::get('/profile/edit', fn() => view('user.profile.edit'))
    ->middleware('auth')
    ->name('profile.edit');

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


