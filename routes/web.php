<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CoursesController;

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

// User Dashboard
Route::get('/dashboard', function () {
    return view('user.user_dashboard');
})->name('dashboard');

// Show Courses 
Route::get('/courses', [CoursesController::class, 'index'])->name('courses.index');
Route::get('/courses/{id}', [CoursesController::class, 'show'])->name('courses.show');

// Show Lessons (placeholder for future implementation)
Route::get('/lessons', function () {
    return view('user.lessons.index');
})->name('lessons.index');


