<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
use App\Http\Controllers\TeacherSectionController;
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

// Admin/Teacher Auth Routes - removed in favor of unified login

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
        
        // Save quiz progress (for regular assessments)
        Route::post('/save-progress', [RegularAssessmentController::class, 'saveProgress'])->name('save-progress');
        
        // Get saved progress (for regular assessments)
        Route::get('/get-progress/{sessionId}/{questionId}', [RegularAssessmentController::class, 'getSavedProgress'])->name('get-progress');
        
        // Clear progress (for regular assessments)
        Route::delete('/clear-progress/{sessionId}', [RegularAssessmentController::class, 'clearSavedProgress'])->name('clear-progress');
        
        // Clear diagnostic session
        Route::post('/clear-diagnostic', [AssessmentController::class, 'clearDiagnosticSession'])->name('clear-diagnostic');
        
                // Assessment session cleanup
        Route::post('/assessment/cleanup', [StudentQuizController::class, 'cleanupAssessment'])->name('assessment.cleanup');
    });
    
    // Gamification API Routes
    Route::prefix('gamification')->name('gamification.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Student\GamificationApiController::class, 'getDashboard'])->name('dashboard');
        Route::get('/leaderboard', [App\Http\Controllers\Student\GamificationApiController::class, 'getLeaderboard'])->name('leaderboard');
        Route::get('/points-history', [App\Http\Controllers\Student\GamificationApiController::class, 'getPointsHistory'])->name('points-history');
        Route::get('/badges', [App\Http\Controllers\Student\GamificationApiController::class, 'getBadges'])->name('badges');
        Route::get('/my-ranking', [App\Http\Controllers\Student\GamificationApiController::class, 'getMyRanking'])->name('my-ranking');
        Route::get('/stats', [App\Http\Controllers\Student\GamificationApiController::class, 'getStats'])->name('stats');
        Route::get('/check-new-badges', [App\Http\Controllers\Student\GamificationApiController::class, 'checkNewBadges'])->name('check-new-badges');
        Route::post('/mark-badges-viewed', [App\Http\Controllers\Student\GamificationApiController::class, 'markBadgesAsViewed'])->name('mark-badges-viewed');
    });
    
    // Assessment progress saving
    Route::post('/assessment/save-progress', [StudentQuizController::class, 'saveAssessmentProgress'])->name('assessment.save-progress');
    
    // Regular quiz results routes (separate from diagnostic assessment routes)
    Route::get('/results/complete/{category}', [RegularAssessmentController::class, 'showQuizComplete'])->name('results.complete');
    
    // Regular quiz review page
    Route::get('/results/review/{category}', [RegularAssessmentController::class, 'showQuizReview'])->name('results.review');
    
    // Get regular quiz results data
    Route::get('/results/data/{assessmentId}', [RegularAssessmentController::class, 'getQuizResultsData'])->name('results.data');
});

// Teacher Routes - Using Admin Auth System
Route::middleware(['admin.auth'])->prefix('teacher')->name('teacher.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Teacher\TeacherDashboardController::class, 'index'])->name('dashboard');
    
    
    // Student Profile Route
Route::get('/students/{student}/profile', [App\Http\Controllers\Teacher\TeacherReviewStudentController::class, 'showProfile'])
    ->name('students.profile');


    // Assessment Review Route
Route::get('/assessments/review/{student}/{assessment}', [App\Http\Controllers\Teacher\TeacherReviewAssessmentController::class, 'reviewAssessment'])
    ->name('assessments.review');

// Section Management
Route::get('/sections', [App\Http\Controllers\Teacher\TeacherSectionController::class, 'index'])->name('sections');
Route::get('/sections/{section}', [App\Http\Controllers\Teacher\TeacherSectionController::class, 'show'])->name('sections.show'); // ADD THIS LINE
Route::get('/sections/students/{section}', [App\Http\Controllers\Teacher\TeacherSectionController::class, 'getSectionStudents'])->name('sections.students');
    
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Assessment Management
    Route::get('/assessments', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'index'])->name('assessments');
    Route::get('/assessments/create', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'create'])->name('assessments.create');
    Route::post('/assessments', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'store'])->name('assessments.store');
    Route::get('/assessments/{assessment}', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'show'])->name('assessments.show');
    Route::get('/assessments/{assessment}/edit', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'edit'])->name('assessments.edit');
    Route::put('/assessments/{assessment}', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'update'])->name('assessments.update');
    Route::delete('/assessments/{assessment}', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'destroy'])->name('assessments.destroy');
    Route::get('/assessments/{assessment}/results', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'results'])->name('assessments.results');
    Route::get('/assessments/{assessment}/student/{student}/attempts', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'getStudentAttempts'])->name('assessments.student.attempts');
    Route::get('/assessments/review-session/{session}', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'reviewSession'])->name('assessments.review-session');
    Route::post('/assessments/{assessment}/archive', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'archive'])->name('assessments.archive');
    Route::post('/assessments/{assessment}/unarchive', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'unarchive'])->name('assessments.unarchive');
    
    // Assessment Assignment API
    Route::get('/assessments/students/{section}', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'getStudents'])->name('assessments.students');
    Route::post('/assessments/assign', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'assign'])->name('assessments.assign');
    Route::delete('/assessments/assignments/{assignment}', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'removeAssignment'])->name('assessments.remove-assignment');
    
    // Question Bank API
    Route::post('/questions/load', [App\Http\Controllers\Teacher\TeacherAssessmentController::class, 'loadQuestions'])->name('questions.load');
    
    // Section Management
    Route::get('/sections', [App\Http\Controllers\Teacher\TeacherSectionController::class, 'index'])->name('sections');
    Route::get('/sections/students/{section}', [App\Http\Controllers\Teacher\TeacherSectionController::class, 'getSectionStudents'])->name('sections.students');
    Route::post('/sections', [App\Http\Controllers\Teacher\TeacherSectionController::class, 'store'])->name('sections.store');
    Route::put('/sections/{section}', [App\Http\Controllers\Teacher\TeacherSectionController::class, 'update'])->name('sections.update');
    Route::post('/sections/{section}/deactivate', [App\Http\Controllers\Teacher\TeacherSectionController::class, 'deactivate'])->name('teacher.sections.deactivate');
    // Student Overview Page (for assessment management)
    Route::get('/students', function () {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return redirect()->route('login');
        }
        return view('admin.teacher.students.index');
    })->name('students');
    
    // Section Student Management API (used by section modals)
    Route::post('/sections/students', [App\Http\Controllers\Teacher\TeacherStudentController::class, 'store'])->name('sections.students.store');
    Route::put('/sections/students/{student}', [App\Http\Controllers\Teacher\TeacherStudentController::class, 'update'])->name('sections.students.update');
    Route::delete('/sections/students/{student}', [App\Http\Controllers\Teacher\TeacherStudentController::class, 'destroy'])->name('sections.students.destroy');
    Route::post('/sections/students/{student}/deactivate', [App\Http\Controllers\Teacher\TeacherStudentController::class, 'deactivate'])->name('sections.students.deactivate');
    Route::post('/sections/students/{student}/activate', [App\Http\Controllers\Teacher\TeacherStudentController::class, 'activate'])->name('sections.students.activate');
    //Remove Student From Section
    Route::post('/sections/{studentId}/remove', [App\Http\Controllers\Teacher\TeacherStudentController::class, 'removeFromSection'])->name('teacher.sections.students.remove');

    // Announcement Management
    Route::resource('announcements', \App\Http\Controllers\Teacher\AnnouncementController::class)->only(['index', 'store', 'destroy']);

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
    
    
    // Profile
    Route::get('/profile', function () {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return redirect()->route('login');
        }
        return view('admin.teacher.profile.index');
    })->name('profile');
});


Route::prefix('admin')
    ->middleware(['admin.auth', 'admin.role:Admin'])
    ->name('admin.')
    ->group(function () {

        /**
         * ─── ADMIN PROFILE ROUTES ──────────────────────────────────────────────
         */
        Route::get('/profile', [App\Http\Controllers\AdminController::class, 'index'])->name('profile.index');
        Route::put('/profile', [App\Http\Controllers\AdminController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [App\Http\Controllers\AdminController::class, 'updatePassword'])->name('profile.update-password');
        Route::delete('/profile', [App\Http\Controllers\AdminController::class, 'destroy'])->name('profile.delete');

        /**
         * ─── MANAGEMENT ROUTES ────────────────────────────────────────────────
         */
        Route::prefix('management')->name('management.')->group(function () {

            /**
             * TEACHER MANAGEMENT
             */
            Route::get('/teachers', [App\Http\Controllers\Admin\AdminTeacherController::class, 'index'])->name('teachers');
            Route::get('/teachers/sections', [App\Http\Controllers\Admin\AdminTeacherController::class, 'getSections'])->name('teachers.sections');
            Route::get('/teachers/{id}', [App\Http\Controllers\Admin\AdminTeacherController::class, 'show'])->name('teachers.show');
            Route::post('/teachers', [App\Http\Controllers\Admin\AdminTeacherController::class, 'store'])->name('teachers.store');
            Route::put('/teachers/{id}', [App\Http\Controllers\Admin\AdminTeacherController::class, 'update'])->name('teachers.update');
            Route::post('/teachers/{id}/archive', [App\Http\Controllers\Admin\AdminTeacherController::class, 'toggleStatus'])->name('teachers.archive');
            Route::delete('/teachers/{id}', [App\Http\Controllers\Admin\AdminTeacherController::class, 'destroy'])->name('teachers.destroy');

            /**
             * ADMIN MANAGEMENT
             */
            Route::get('/admins', [App\Http\Controllers\admin\AdminManagementController::class, 'index'])->name('admins');
            Route::post('/admins', [App\Http\Controllers\admin\AdminManagementController::class, 'store'])->name('admins.store');
            Route::get('/admins/{id}/edit', [App\Http\Controllers\admin\AdminManagementController::class, 'edit'])->name('admins.edit');
            Route::put('/admins/{id}', [App\Http\Controllers\admin\AdminManagementController::class, 'update'])->name('admins.update');
            Route::put('/admins/{id}/status', [App\Http\Controllers\admin\AdminManagementController::class, 'archive'])->name('admins.archive');
            Route::delete('/admins/{id}', [App\Http\Controllers\admin\AdminManagementController::class, 'destroy'])->name('admins.destroy');

            /**
             * STUDENT MANAGEMENT
             */
            // Main student management view
            Route::get('/students', function () {
                return view('admin.admin.management.student-management');
            })->name('students');

            // Section-based student routes
            Route::get('/sections/{section}/students', [App\Http\Controllers\Admin\StudentManagementController::class, 'index'])
                ->name('sections.students');
            Route::post('/sections/{section}/students', [App\Http\Controllers\Admin\StudentManagementController::class, 'store'])
                ->name('sections.students.store');

            // Student-level actions
            Route::get('/students/{student}/edit', [App\Http\Controllers\Admin\StudentManagementController::class, 'edit'])
                ->name('students.edit');
            Route::put('/students/{student}', [App\Http\Controllers\Admin\StudentManagementController::class, 'update'])
                ->name('students.update');
            Route::post('/students/{student}/archive', [App\Http\Controllers\Admin\StudentManagementController::class, 'archive'])
                ->name('students.archive');
            Route::post('/students/{student}/inactive', [App\Http\Controllers\Admin\StudentManagementController::class, 'inactive'])
                ->name('students.inactive');
            Route::delete('/students/{student}', [App\Http\Controllers\Admin\StudentManagementController::class, 'destroy'])
                ->name('students.destroy');
            Route::post('/students/{student}/restore', [App\Http\Controllers\Admin\StudentManagementController::class, 'restore'])
                ->name('students.restore');

            /**
             * ALL STUDENTS (GLOBAL VIEW)
             */
            Route::get('/all-students', [App\Http\Controllers\Admin\StudentManagementController::class, 'allStudents'])
                ->name('all-students');
            Route::post('/all-students', [App\Http\Controllers\Admin\StudentManagementController::class, 'storeAllStudents'])
                ->name('all-students.store');
            Route::get('/all-students/{id}/edit', [App\Http\Controllers\Admin\StudentManagementController::class, 'editAllStudents'])
                ->name('all-students.edit');
            Route::put('/all-students/{id}', [App\Http\Controllers\Admin\StudentManagementController::class, 'updateAllStudents'])
                ->name('all-students.update');
            // Section Management
            Route::get('/sections', [App\Http\Controllers\Admin\AdminSectionController::class, 'index'])->name('sections');
            Route::get('/sections/admin', [App\Http\Controllers\Admin\AdminSectionController::class, 'getAvailableTeachers'])->name('sections.teachers');
            Route::post('/sections', [App\Http\Controllers\Admin\AdminSectionController::class, 'store'])->name('sections.store');
            Route::put('/sections/{section}', [App\Http\Controllers\Admin\AdminSectionController::class, 'update'])->name('sections.update');
            Route::delete('/sections/{section}', [App\Http\Controllers\Admin\AdminSectionController::class, 'destroy'])->name('sections.destroy');
            Route::post('/sections/{section}/archive', [App\Http\Controllers\Admin\AdminSectionController::class, 'archive'])->name('sections.archive');
            Route::post('/sections/{section}/activate', [App\Http\Controllers\Admin\AdminSectionController::class, 'activate'])->name('sections.activate');
            // Questions Management
            Route::get('/questions', [App\Http\Controllers\admin\QuestionController::class, 'index'])->name('questions');
            Route::get('/questions/data', [App\Http\Controllers\admin\QuestionController::class, 'data'])->name('questions.data');
            Route::post('/questions', [App\Http\Controllers\admin\QuestionController::class, 'store'])->name('questions.store');
            Route::put('/questions/{question}', [App\Http\Controllers\admin\QuestionController::class, 'update'])->name('questions.update');
            Route::delete('/questions/{question}', [App\Http\Controllers\admin\QuestionController::class, 'destroy'])->name('questions.destroy');
        });
    });

// Admin Dashboard (placeholder)
Route::get('/admin/dashboard', function () {
    return view('admin.admin.index');
})->middleware(['admin.auth', 'admin.role:Admin'])->name('admin.dashboard');

Route::get('/profile', function() {
    $user = Auth::guard('student')->user();
    $profile = $user->studentProfile;
    return view('student.profile.student-profile', compact('profile'));
})->name('student.profile');
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
    Route::get('/leaderboard/section', [LeaderboardController::class, 'getSectionLeaderboard'])->name('leaderboard.section');
    Route::get('/leaderboard/school', [LeaderboardController::class, 'getSchoolLeaderboard'])->name('leaderboard.school');
    
    // Sections (Student view)
    Route::get('/my-sections', [SectionController::class, 'index'])->name('sections.index');
    Route::get('/sections/data', [SectionController::class, 'getSectionsData'])->name('sections.data');
    
    // Teacher-created assessments
    Route::get('/teacher-assessments', [App\Http\Controllers\Student\StudentTeacherAssessmentController::class, 'index'])->name('teacher-assessments.index');
    Route::get('/teacher-assessments/{assessment}', [App\Http\Controllers\Student\StudentTeacherAssessmentController::class, 'show'])->name('teacher-assessments.show');
    Route::get('/teacher-assessments/{assessment}/quiz', [App\Http\Controllers\Student\StudentTeacherAssessmentController::class, 'startQuiz'])->name('teacher-assessments.start');
    Route::post('/teacher-assessments/{assessment}/submit-answer', [App\Http\Controllers\Student\StudentTeacherAssessmentController::class, 'submitAnswer'])->name('teacher-assessments.submit-answer');
    Route::post('/teacher-assessments/{assessment}/complete', [App\Http\Controllers\Student\StudentTeacherAssessmentController::class, 'completeQuiz'])->name('teacher-assessments.complete');
    Route::post('/teacher-assessments/{assessment}/retake', [App\Http\Controllers\Student\StudentTeacherAssessmentController::class, 'retakeQuiz'])->name('teacher-assessments.retake');
    Route::get('/teacher-assessments/{assessment}/results', [App\Http\Controllers\Student\StudentTeacherAssessmentController::class, 'results'])->name('teacher-assessments.results');
    
    // Profile routes
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update.avatar');
Route::get('/profile/avatar', [ProfileController::class, 'getCurrentAvatar'])->name('profile.get.avatar');
Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('student.update-password');
Route::post('/student/logout', [ProfileController::class, 'logout'])->name('student.logout');
    
    // Login streak routes
    Route::get('/check-login-streak', [App\Http\Controllers\LoginStreakController::class, 'checkStreak'])->name('login-streak.check');
    Route::get('/streak-data', [App\Http\Controllers\LoginStreakController::class, 'getStreakData'])->name('login-streak.data');
    
    // Login streak page
    Route::get('/login-streak', [App\Http\Controllers\LoginStreakController::class, 'showStreakPage'])->name('login-streak.page');

    // Leaderboard Badge Testing Routes (Local only)
    if (app()->environment('local')) {
        Route::get('/test-weekly-badges', [App\Http\Controllers\LeaderboardBadgeController::class, 'testWeeklyAward'])->name('test-weekly-badges');
        Route::get('/test-monthly-badges', [App\Http\Controllers\LeaderboardBadgeController::class, 'testMonthlyAward'])->name('test-monthly-badges');
    }
});