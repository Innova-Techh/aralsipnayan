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
use App\Http\Controllers\Student\RegularAssessmentController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\RankController;
use App\Http\Controllers\LevelUpController;

// Homepage
Route::get('/', function () {
    return view('homepage');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin/Teacher Auth Routes
Route::get('/adminlogin', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/adminlogin', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// General dashboard redirect (based on role)
Route::get('/dashboard', function() {
    // Check student guard first
    if (Auth::guard('student')->check()) {
        return redirect()->route('student.dashboard');
    }
    
    // Check admin guard
    if (Auth::guard('admin')->check()) {
        $user = Auth::guard('admin')->user();
        switch($user->role) {
            case 'Admin':
                return redirect()->route('admin.dashboard');
            case 'Teacher':
                return redirect()->route('teacher.dashboard');
            default:
                return redirect('/');
        }
    }
    
    // No one is authenticated, redirect to login
    return redirect()->route('login');
})->name('dashboard');

// Rank Controller Routes
Route::middleware('auth:student')->group(function () {
    Route::get('/api/user-rank', [RankController::class, 'getUserRankData'])->name('rank.user-data');
    Route::post('/api/award-xp', [RankController::class, 'awardXP'])->name('rank.award-xp');
});

// Level Up System Routes
// Route::middleware(['auth:student'])->group(function () {
//     // Get current progress
//     Route::get('/level-up/progress', [LevelUpController::class, 'getCurrentProgress'])->name('level-up.progress');
    
//     // Add XP and check for level up
//     Route::post('/level-up/add-xp', [LevelUpController::class, 'addXP'])->name('level-up.add-xp');
// });

// Student Onboarding Routes (no middleware restrictions)
Route::prefix('student/onboarding')->name('student.onboarding.')->group(function () {
    Route::get('/welcome', [OnboardingController::class, 'showWelcome'])->name('welcome');
    Route::get('/start', [OnboardingController::class, 'startOnboarding'])->name('start');
    Route::get('/avatar', [OnboardingController::class, 'showAvatarSelection'])->name('avatar');
    Route::post('/complete', [OnboardingController::class, 'completeOnboarding'])->name('complete');
});

// Protected Student Routes (require completed onboarding)
Route::middleware(['student.auth', 'student.role:Student'])->prefix('student')->name('student.')->group(function () {
    
    // Dashboard - check onboarding completion
    Route::get('/dashboard', function() {
        $user = Auth::guard('student')->user();
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
    
    // Assessment refresh route
    Route::post('/assessments/{category}/refresh', [AssessmentController::class, 'refreshAssessments'])->name('assessments.refresh');
    
    // Assessment complete page
    Route::get('/assessments/complete/{category}', [AssessmentController::class, 'showComplete'])->name('assessments.complete');
    
    // Assessment review page
    Route::get('/assessments/review/{category}', [AssessmentController::class, 'showReview'])->name('assessments.review');
    
    // Quiz routes
    Route::prefix('quiz')->name('quiz.')->group(function () {
        // Check for active assessments
        Route::post('/check-active', [RegularAssessmentController::class, 'checkActiveAssessments'])->name('check-active');
        
        // Check for active diagnostics
        Route::post('/check-active-diagnostics', [AssessmentController::class, 'checkActiveDiagnostics'])->name('check-active-diagnostics');
        
        // Resume assessment from list
        Route::post('/resume-assessment', [RegularAssessmentController::class, 'resumeAssessmentFromList'])->name('resume-assessment');
        
        // Start regular assessment
        Route::post('/start-assessment', [RegularAssessmentController::class, 'startAssessment'])->name('start-assessment');
        
        // Submit regular assessment answer
        Route::post('/submit-regular', [RegularAssessmentController::class, 'submitAnswer'])->name('submit-regular');
        
        // Start diagnostic for a category
        Route::get('/diagnostic/{category}', [AssessmentController::class, 'startDiagnostic'])->name('diagnostic');
        
        // Show quiz interface (diagnostic or regular)
        Route::get('/{category}', [StudentQuizController::class, 'show'])->name('show');
        
        // Submit diagnostic answer
        Route::post('/diagnostic/submit', [AssessmentController::class, 'submitDiagnosticAnswer'])->name('diagnostic.submit');
        
        // Submit regular assessment answer
        Route::post('/submit', [StudentQuizController::class, 'submitAnswer'])->name('submit');
        
        // Get hint for current question
        Route::post('/hint', [StudentQuizController::class, 'getHint'])->name('hint');
        
        // Save quiz progress
        Route::post('/save-progress', [AssessmentController::class, 'saveProgress'])->name('save-progress');
        
        // Get saved progress
        Route::get('/get-progress/{sessionId}/{questionId}', [AssessmentController::class, 'getProgress'])->name('get-progress');
        
        // Clear progress
        Route::delete('/clear-progress/{sessionId}', [AssessmentController::class, 'clearProgress'])->name('clear-progress');
        
        // Clear diagnostic session
        Route::post('/clear-diagnostic', [AssessmentController::class, 'clearDiagnosticSession'])->name('clear-diagnostic');
        
        // Assessment session cleanup
        Route::post('/assessment/cleanup', [StudentQuizController::class, 'cleanupAssessment'])->name('assessment.cleanup');
        
        // Assessment progress saving
        Route::post('/assessment/save-progress', [StudentQuizController::class, 'saveAssessmentProgress'])->name('assessment.save-progress');
        
        // Regular quiz results routes (separate from diagnostic assessment routes)
        Route::get('/results/complete/{category}', [RegularAssessmentController::class, 'showQuizComplete'])->name('results.complete');
        
        // Regular quiz review page
        Route::get('/results/review/{category}', [RegularAssessmentController::class, 'showQuizReview'])->name('results.review');
        
        // Get regular quiz results data
        Route::get('/results/data/{assessmentId}', [RegularAssessmentController::class, 'getQuizResultsData'])->name('results.data');
    });
});

// Teacher Routes - Using Admin Auth System
Route::middleware(['admin.auth'])->prefix('teacher')->name('teacher.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        // Check if user is authenticated and has Teacher role
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }
        
        if (Auth::guard('admin')->user()->role !== 'Teacher') {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->withErrors(['access' => 'Teacher access required.']);
        }
        
        return view('admin.teacher.index');
    })->name('dashboard');
    
    // Assessment Management
    Route::get('/assessments', [App\Http\Controllers\Teacher\AssessmentController::class, 'index'])->name('assessments');
    Route::get('/assessments/create', [App\Http\Controllers\Teacher\AssessmentController::class, 'create'])->name('assessments.create');
    
    // Assessment Assignment API
    Route::get('/assessments/students/{section}', [App\Http\Controllers\Teacher\AssessmentController::class, 'getStudentsBySection'])->name('assessments.students');
    Route::post('/assessments/assign', [App\Http\Controllers\Teacher\AssessmentController::class, 'assignAssessment'])->name('assessments.assign');
    
    // Student Management
    Route::get('/students', function () {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return redirect()->route('admin.login');
        }
        return view('admin.teacher.students.index');
    })->name('students');
    
    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Teacher\AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/assessment/{assessmentId}', [App\Http\Controllers\Teacher\AnalyticsController::class, 'showAssessmentDetails'])->name('analytics.assessment.details');
    
    // Debug route
    Route::get('/debug-assessments', function() {
        $assessments = DB::table('assessments')->get();
        $diagnostics = DB::table('diagnostic_sessions')->get();
        
        return response()->json([
            'assessments_count' => $assessments->count(),
            'assessments' => $assessments->take(5),
            'diagnostics_count' => $diagnostics->count(), 
            'diagnostics' => $diagnostics->take(5)
        ]);
    });
    
    // Section Management
    Route::get('/sections', function () {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return redirect()->route('admin.login');
        }
        return view('admin.teacher.sections.index');
    })->name('sections');
    
    // Profile
    Route::get('/profile', function () {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return redirect()->route('admin.login');
        }
        return view('admin.teacher.profile.index');
    })->name('profile');
});

// Admin Dashboard (placeholder)
Route::get('/admin/dashboard', function () {
    return view('admin.admin.index');
})->middleware(['admin.auth', 'admin.role:Admin'])->name('admin.dashboard');


// Backward compatibility routes for old assessment references (redirects to student routes)
Route::group([], function () {
    // Legacy assessment routes (redirects to student assessments)
    Route::get('/assessments', function() {
        if (Auth::guard('student')->check() && Auth::guard('student')->user()->role === 'Student') {
            return redirect()->route('student.assessments');
        }
        return redirect()->route('dashboard');
    })->name('assessments.index');
    
    // Legacy assessment category route
    Route::get('/assessments/{category}', function($category) {
        if (Auth::guard('student')->check() && Auth::guard('student')->user()->role === 'Student') {
            return redirect()->route('student.assessments.category', $category);
        }
        return redirect()->route('dashboard');
    })->name('assessments.category');
    
    // Legacy quiz start route
    Route::get('/quiz/{category}', function($category) {
        if (Auth::guard('student')->check() && Auth::guard('student')->user()->role === 'Student') {
            return redirect()->route('student.quiz.show', $category);
        }
        return redirect()->route('dashboard');
    })->name('quiz.start');
});

// Other protected routes (require auth + completed onboarding for students)
Route::middleware(['student.auth'])->group(function () {
    
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
    
    // Login streak routes
    Route::get('/check-login-streak', [App\Http\Controllers\LoginStreakController::class, 'checkStreak'])->name('login-streak.check');
    Route::get('/streak-data', [App\Http\Controllers\LoginStreakController::class, 'getStreakData'])->name('login-streak.data');
    
    // Login streak page
    Route::get('/login-streak', [App\Http\Controllers\LoginStreakController::class, 'showStreakPage'])->name('login-streak.page');
});