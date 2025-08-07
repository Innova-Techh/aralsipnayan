<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaderboardController;

//show homepage
Route::get('/', function () {
    return view('homepage');
});

// Show login page
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

//Test route to check if timestamp is synch with Philippine Timezone
//Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
//Route::post('/users/store', [UserController::class, 'store'])->name('users.store');

// Dashboard home
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');

// User dashboard routes
Route::prefix('user')->name('user.')->group(function () {
    Route::get('/courses', [CoursesController::class, 'index'])->name('courses');
    Route::get('/courses/{id}', [CoursesController::class, 'show'])->name('courses.show');
});

// Alternative routes (you can choose which structure you prefer)
Route::get('/courses', [CoursesController::class, 'index'])->name('courses');
Route::get('/courses/{id}', [CoursesController::class, 'show'])->name('courses.show');
Route::get('/lessons', function () {
    return view('user.lessons');
})->name('lessons.index');

// Missing routes that are referenced in the layout
Route::get('/assessments', function () {
    return view('user.assessments');
})->name('assessments.index');

Route::get('/achievements', [App\Http\Controllers\AchievementController::class, 'index'])->name('achievements.index');

Route::get('/progression', function () {
    return view('user.progression');
})->name('progression.index');

Route::get('/leaderboard', [App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard.index');
Route::get('/leaderboard/data', [App\Http\Controllers\LeaderboardController::class, 'getLeaderboardData'])->name('leaderboard.data');

Route::get('/resources', function () {
    return view('user.resources');
})->name('resources.index');

Route::get('/profile/edit', function () {
    return view('user.profile.edit');
})->name('profile.edit');

// Logout route
Route::post('/logout', function () {
    return redirect('/');
})->name('logout');


