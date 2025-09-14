<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\AlgorithmController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ProfileController;
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
Route::get('/assessments', [App\Http\Controllers\AssessmentController::class, 'index'])
    ->middleware('auth')
    ->name('assessments.index');

Route::get('/assessments/{category}', [App\Http\Controllers\AssessmentController::class, 'showCategory'])
    ->middleware('auth')
    ->name('assessments.category');

    Route::get('/quiz/{category}', [QuizController::class, 'start'])->name('quiz.start');


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

// Sections
Route::get('/sections', [SectionController::class, 'index'])
    ->middleware('auth')
    ->name('sections.index');
Route::get('/sections/data', [SectionController::class, 'getSectionsData'])
    ->middleware('auth')
    ->name('sections.data');

// Profile routes
Route::get('/profile/edit', [ProfileController::class, 'edit'])
    ->middleware('auth')
    ->name('profile.edit');
Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
    ->middleware('auth')
    ->name('profile.update.avatar');
Route::get('/profile/avatar', [ProfileController::class, 'getCurrentAvatar'])
    ->middleware('auth')
    ->name('profile.get.avatar');

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


//ALGORITHM INTEGRATION
Route::post('/run-bkt', [App\Http\Controllers\AssessmentController::class, 'runBkt']);