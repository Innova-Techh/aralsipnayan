<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema; // Add this import

class OnboardingController extends Controller
{
    /**
     * Show welcome page
     */
    public function showWelcome()
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            return redirect()->route('login');
        }
        
        $student = $user->studentProfile;
        
        // If onboarding already completed, redirect to dashboard
        if ($student && $student->has_completed_onboarding) {
            return redirect()->route('student.dashboard');
        }
        
        return view('student.onboarding.welcome', compact('student'));
    }

    /**
     * Start onboarding process (redirect to avatar selection)
     */
    public function startOnboarding()
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            return redirect()->route('login');
        }
        
        return redirect()->route('student.onboarding.avatar');
    }

    /**
     * Show avatar selection page
     */
    public function showAvatarSelection()
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            return redirect()->route('login');
        }
        
        $student = $user->studentProfile;
        
        // If onboarding already completed, redirect to dashboard
        if ($student && $student->has_completed_onboarding) {
            return redirect()->route('student.dashboard');
        }
        
        // Get current avatar URL for display
        $userAvatarUrl = $student ? $student->avatar_url : null;
        
        // Fix: Use the existing profile edit view
        return view('student.profile.edit', compact('student', 'userAvatarUrl'));
    }

    /**
     * Complete onboarding (called from avatar selection)
     */
    public function completeOnboarding(Request $request)
    {
        // Debug log
        Log::info('Onboarding completion started', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $request->validate([
            'avatar' => 'required|string'
        ]);

        $user = Auth::user();
        
        if (!$user || !$user->isStudent()) {
            Log::error('Unauthorized onboarding attempt', ['user' => $user ? $user->toArray() : null]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }
        
        try {
            DB::beginTransaction();
            
            // Get or create student profile
            $profile = $user->studentProfile;
            if (!$profile) {
                Log::error('Student profile not found for user', ['user_id' => $user->id]);
                throw new \Exception('Student profile not found');
            }

            Log::info('Updating student profile', [
                'profile_id' => $profile->id,
                'avatar' => $request->avatar
            ]);
            
            // Prepare update data
            $updateData = [
                'avatar_url' => "/images/profile/{$request->avatar}",
            ];
            
            // Add onboarding fields if they exist
            if (Schema::hasColumn('student_profile', 'has_completed_onboarding')) {
                $updateData['has_completed_onboarding'] = true;
            }
            
            if (Schema::hasColumn('student_profile', 'onboarding_completed_at')) {
                $updateData['onboarding_completed_at'] = now();
            }
            
            // Update profile
            $profile->update($updateData);
            
            // Initialize BKT mastery records for all competencies
            $this->initializeBKTMastery($user->id);
            
            DB::commit();
            
            Log::info('Onboarding completed successfully', ['user_id' => $user->id]);
            
            return response()->json([
                'success' => true,
                'message' => "success.",
                'redirect_url' => route('student.dashboard')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Onboarding completion failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initialize BKT mastery records for all competencies
     */
    private function initializeBKTMastery(int $userId)
    {
        // Check if student_mastery table exists first
        if (!Schema::hasTable('student_mastery')) {
            Log::warning('student_mastery table does not exist, skipping BKT initialization');
            return;
        }
        
        $competencies = [
            'number_algebra',
            'measurement_geometry', 
            'data_probability'
        ];
        
        foreach ($competencies as $competency) {
            // Generate unique mastery ID
            $masteryId = sprintf("STU%03d-%s-%s-%03d", $userId, $competency, date('Ymd'), rand(100, 999));
            
            // Check if mastery record already exists
            $existingMastery = DB::table('student_mastery')
                ->where('user_id', $userId)
                ->where('competency', $competency)
                ->first();
                
            if (!$existingMastery) {
                try {
                    DB::table('student_mastery')->insert([
                        'mastery_id' => $masteryId,
                        'user_id' => $userId,
                        'competency' => $competency,
                        'current_difficulty' => 'beginner',
                        
                        // Mastery score components (starting values)
                        'accuracy_score' => 0.0000,
                        'bkt_score' => 0.5000, // Starting BKT score
                        'final_mastery_score' => 22.50, // (0.55 × 0) + (0.45 × 0.5) × 100
                        
                        // BKT parameters (defaults)
                        'prior_knowledge' => 0.1000,
                        'learn_rate' => 0.3000,
                        'slip_rate' => 0.1000,
                        'guess_rate' => 0.2500,
                        
                        // Diagnostic status
                        'has_taken_diagnostic' => false,
                        'diagnostic_completed_at' => null,
                        
                        // Performance tracking
                        'total_questions_answered' => 0,
                        'correct_answers' => 0,
                        'total_assessments_taken' => 0,
                        'cumulative_time_score' => 0.0000,
                        
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    Log::info("Initialized BKT mastery for user {$userId}, competency: {$competency}");
                } catch (\Exception $e) {
                    Log::error("Failed to initialize BKT mastery for user {$userId}, competency: {$competency}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        Log::info("BKT mastery initialization completed for user {$userId}");
    }
}