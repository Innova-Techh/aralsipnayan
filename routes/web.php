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
use App\Http\Controllers\Student\OnboardingController;
use App\Http\Controllers\Student\AssessmentController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;

// Homepage
Route::get('/', function () {
    return view('homepage');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Admin/Teacher login page
Route::get('/adminlogin', function () {
    return view('admin.auth.login');
})->name('admin.login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/adminlogin', [AuthController::class, 'adminLogin'])->name('admin.login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/admin/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');


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

// Student Onboarding Routes (no middleware restrictions)
Route::prefix('student/onboarding')->name('student.onboarding.')->group(function () {
    Route::get('/welcome', [OnboardingController::class, 'showWelcome'])->name('welcome');
    Route::get('/start', [OnboardingController::class, 'startOnboarding'])->name('start');
    Route::get('/avatar', [OnboardingController::class, 'showAvatarSelection'])->name('avatar');
    Route::post('/complete', [OnboardingController::class, 'completeOnboarding'])->name('complete');
});

// Protected Student Routes (require completed onboarding)
Route::middleware(['auth', 'role:Student'])->prefix('student')->name('student.')->group(function () {
    
    // Dashboard - check onboarding completion
    Route::get('/dashboard', function() {
        $user = Auth::user();
        $profile = $user->studentProfile;
        
        // If onboarding not completed, redirect to welcome
        if (!$profile || !$profile->has_completed_onboarding) {
            return redirect()->route('student.onboarding.welcome');
        }
        
        return app(DashboardController::class)->index();
    })->name('dashboard');
    
    // Assessments - main page
    Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments');
    
    // Assessment category routes
    Route::get('/assessments/{category}', [AssessmentController::class, 'showCategory'])->name('assessments.category');
    
    // Assessment complete page
    Route::get('/assessments/complete/{category}', [AssessmentController::class, 'showComplete'])->name('assessments.complete');
    
    // Assessment review page
    Route::get('/assessments/review/{category}', [AssessmentController::class, 'showReview'])->name('assessments.review');
    
    // Quiz routes
    Route::prefix('quiz')->name('quiz.')->group(function () {
        // Start diagnostic for a category
        Route::get('/diagnostic/{category}', [AssessmentController::class, 'startDiagnostic'])->name('diagnostic');
        
        // Show quiz interface (diagnostic or regular)
        Route::get('/{category}', [StudentQuizController::class, 'show'])->name('show');
        
        // Submit diagnostic answer
        Route::post('/diagnostic/submit', [AssessmentController::class, 'submitDiagnosticAnswer'])->name('diagnostic.submit');
        
        // Submit regular assessment answer
        Route::post('/submit', [StudentQuizController::class, 'submitAnswer'])->name('submit');
        
        // Save quiz progress
        Route::post('/save-progress', [AssessmentController::class, 'saveProgress'])->name('save-progress');
        
        // Get saved progress
        Route::get('/get-progress/{sessionId}/{questionId}', [AssessmentController::class, 'getProgress'])->name('get-progress');
        
        // Clear progress
        Route::delete('/clear-progress/{sessionId}', [AssessmentController::class, 'clearProgress'])->name('clear-progress');
    });
});

// Teacher Dashboard (placeholder view)
Route::get('/teacher/dashboard', function () {
    return view('admin.teacher.index');
})->middleware(['auth', 'role:Teacher'])->name('teacher.dashboard');

// Admin Dashboard (placeholder view)
Route::get('/admin/dashboard', function () {
    return view('admin.admin.index');
})->middleware(['auth', 'role:Admin'])->name('admin.dashboard');

// Backward compatibility routes for old assessment references (redirects to student routes)
Route::middleware(['auth'])->group(function () {
    // Legacy assessment routes (redirects to student assessments)
    Route::get('/assessments', function() {
        if (Auth::user()->role === 'Student') {
            return redirect()->route('student.assessments');
        }
        return redirect()->route('dashboard');
    })->name('assessments.index');
    
    // Legacy assessment category route
    Route::get('/assessments/{category}', function($category) {
        if (Auth::user()->role === 'Student') {
            return redirect()->route('student.assessments.category', $category);
        }
        return redirect()->route('dashboard');
    })->name('assessments.category');
    
    // Legacy quiz start route
    Route::get('/quiz/{category}', function($category) {
        if (Auth::user()->role === 'Student') {
            return redirect()->route('student.quiz.show', $category);
        }
        return redirect()->route('dashboard');
    })->name('quiz.start');
});

// Other protected routes (require auth + completed onboarding for students)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard stats
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    
    // Achievements
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
    
    // Progression
    Route::get('/progression', fn() => view('user.progression'))->name('progression.index');
    
    // Leaderboard
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/leaderboard/data', [LeaderboardController::class, 'getLeaderboardData'])->name('leaderboard.data');
    
    // Sections
    Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::get('/sections/data', [SectionController::class, 'getSectionsData'])->name('sections.data');
    
    // Profile routes
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update.avatar');
    Route::get('/profile/avatar', [ProfileController::class, 'getCurrentAvatar'])->name('profile.get.avatar');
});