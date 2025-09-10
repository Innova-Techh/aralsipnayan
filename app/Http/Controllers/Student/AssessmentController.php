<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    /**
     * Show assessments page
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            return redirect()->route('login');
        }
        
        $student = $user->studentProfile;
        
        // Check if onboarding is completed
        if (!$student || !$student->has_completed_onboarding) {
            return redirect()->route('student.onboarding.welcome');
        }
        
        // Mark that student has viewed assessments for the first time
        if (!$student->has_viewed_assessments) {
            $student->update([
                'has_viewed_assessments' => true,
                'first_assessment_view_at' => now()
            ]);
        }
        
        // Get student mastery data for all competencies
        $masteries = DB::table('student_mastery')
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('competency');
        
        // Check which competencies need diagnostic
        $needsDiagnostic = [];
        $competencies = ['number_algebra', 'measurement_geometry', 'data_probability'];
        
        foreach ($competencies as $competency) {
            $mastery = $masteries->get($competency);
            if (!$mastery || !$mastery->has_taken_diagnostic) {
                $needsDiagnostic[] = $competency;
            }
        }
        
        return view('student.assessments', [
            'student' => $student,
            'masteries' => $masteries,
            'needsDiagnostic' => $needsDiagnostic,
            'competencies' => $competencies
        ]);
    }
    
    /**
     * Show assessment category page
     */
    public function showCategory($category)
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        
        // Check if onboarding is completed
        if (!$student || !$student->has_completed_onboarding) {
            return redirect()->route('student.onboarding.welcome');
        }
        
        // Validate category
        $validCategories = ['number_algebra', 'measurement_geometry', 'data_probability'];
        if (!in_array($category, $validCategories)) {
            return redirect()->route('student.assessments')->with('error', 'Invalid assessment category.');
        }
        
        // Get mastery data for this category
        $mastery = DB::table('student_mastery')
            ->where('user_id', $user->id)
            ->where('competency', $category)
            ->first();
        
        return view('student.assessment-list', [
            'student' => $student,
            'category' => $category,
            'mastery' => $mastery
        ]);
    }
    
    /**
     * Start assessment (diagnostic or adaptive)
     */
    public function startAssessment(Request $request)
    {
        $request->validate([
            'competency' => 'required|in:number_algebra,measurement_geometry,data_probability'
        ]);
        
        $user = Auth::user();
        $competency = $request->competency;
        
        // Check if onboarding is completed
        $student = $user->studentProfile;
        if (!$student || !$student->has_completed_onboarding) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete onboarding first.',
                'redirect' => route('student.onboarding.welcome')
            ], 403);
        }
        
        // Get mastery record
        $mastery = DB::table('student_mastery')
            ->where('user_id', $user->id)
            ->where('competency', $competency)
            ->first();
        
        if (!$mastery) {
            return response()->json([
                'success' => false,
                'message' => 'Mastery record not found. Please contact support.'
            ], 404);
        }
        
        try {
            // Determine assessment type
            $assessmentType = !$mastery->has_taken_diagnostic ? 'Diagnostic' : 'Adaptive';
            
            // Call Python BKT script to start assessment
            $scriptPath = public_path('algorithm/bkt_integration.py');
            $command = "python {$scriptPath} start_assessment {$user->id} {$competency} {$assessmentType}";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && $result['success']) {
                return response()->json([
                    'success' => true,
                    'session_id' => $result['session_id'],
                    'assessment_type' => $assessmentType,
                    'questions' => $result['questions'],
                    'current_mastery' => $result['initial_mastery'],
                    'diagnostic_mode' => $assessmentType === 'Diagnostic'
                ]);
            } else {
                throw new \Exception($result['message'] ?? 'Failed to start assessment');
            }
            
        } catch (\Exception $e) {
            Log::error('Assessment start failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to start assessment. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Submit answer and get next question
     */
    public function submitAnswer(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'question_id' => 'required|integer',
            'answer' => 'required|string',
            'time_taken' => 'required|integer|min:1',
        ]);
        
        $user = Auth::user();
        
        try {
            // Call Python BKT script to record answer
            $scriptPath = public_path('algorithm/bkt_integration.py');
            $command = "python {$scriptPath} record_answer {$user->id} " . 
                      "{$request->session_id} {$request->question_id} " . 
                      "'{$request->answer}' {$request->time_taken}";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && $result['success']) {
                return response()->json([
                    'success' => true,
                    'is_correct' => $result['is_correct'],
                    'correct_answer' => $result['correct_answer'],
                    'explanation' => $result['explanation'] ?? null,
                    'updated_mastery' => $result['updated_mastery'],
                    'next_question' => $result['next_question'] ?? null,
                    'assessment_complete' => $result['assessment_complete'] ?? false
                ]);
            } else {
                throw new \Exception($result['message'] ?? 'Failed to process answer');
            }
            
        } catch (\Exception $e) {
            Log::error('Answer submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process answer. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Complete assessment session
     */
    public function completeAssessment(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);
        
        $user = Auth::user();
        
        try {
            // Call Python BKT script to complete session
            $scriptPath = public_path('algorithm/bkt_integration.py');
            $command = "python {$scriptPath} complete_session {$user->id} {$request->session_id}";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && $result['success']) {
                return response()->json([
                    'success' => true,
                    'final_mastery' => $result['final_mastery'],
                    'difficulty_level' => $result['difficulty_level'],
                    'diagnostic_completed' => $result['diagnostic_completed'] ?? false,
                    'performance_summary' => $result['performance_summary']
                ]);
            } else {
                throw new \Exception($result['message'] ?? 'Failed to complete assessment');
            }
            
        } catch (\Exception $e) {
            Log::error('Assessment completion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete assessment. Please try again.'
            ], 500);
        }
    }
}