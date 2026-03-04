<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\TeacherAssessmentSession;
use App\Models\TeacherQuizResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemAnalysisController extends Controller
{
    /**
     * Display item analysis for a specific assessment
     */
    public function show(Assessment $assessment)
    {
        $teacher = Auth::guard('admin')->user();
        
        // Verify teacher owns this assessment
        if ($assessment->created_by !== $teacher->id) {
            abort(403, 'You do not have permission to view this analysis.');
        }

        // Get all completed sessions for this assessment
        $completedSessions = TeacherAssessmentSession::where('teacher_assessment_id', $assessment->id)
            ->where('status', 'completed')
            ->with(['user.studentProfile'])
            ->get();

        // Get unique sections from assignments
        $sections = DB::table('teacher_assessment_assignments')
            ->where('assessment_id', $assessment->id)
            ->whereNotNull('section')
            ->distinct()
            ->pluck('section')
            ->toArray();

        // If no section-wide assignments, get sections from assigned students
        if (empty($sections)) {
            $sections = DB::table('teacher_assessment_assignments')
                ->join('student_profile', 'teacher_assessment_assignments.student_id', '=', 'student_profile.user_id')
                ->where('teacher_assessment_assignments.assessment_id', $assessment->id)
                ->whereNotNull('teacher_assessment_assignments.student_id')
                ->distinct()
                ->pluck('student_profile.section')
                ->toArray();
        }

        // Get all unique students who completed the assessment
        $students = [];
        foreach ($completedSessions as $session) {
            $userId = $session->user_id;
            if (!isset($students[$userId])) {
                $studentName = 'Unknown Student';
                if ($session->user && $session->user->studentProfile) {
                    $profile = $session->user->studentProfile;
                    $studentName = trim($profile->firstname . ' ' . $profile->lastname);
                }
                $students[$userId] = [
                    'id' => $userId,
                    'name' => $studentName
                ];
            }
        }

        $totalExaminees = count($students);

        // Get all responses with question details
        $responses = TeacherQuizResponse::whereIn('session_id', $completedSessions->pluck('session_id'))
            ->get();

        // Get unique question IDs from responses
        $questionIds = $responses->pluck('question_id')->unique();

        // Fetch question details from the questions table
        $questionsData = DB::table('questions')
            ->whereIn('question_id', $questionIds)
            ->get()
            ->keyBy('question_id');

        // Group responses by question
        $responsesByQuestion = $responses->groupBy('question_id');

        // Prepare item analysis data
        $itemAnalysis = [];
        $itemNumber = 1;
        
        foreach ($responsesByQuestion as $questionId => $questionResponses) {
            $questionData = $questionsData->get($questionId);
            $questionText = $questionData ? $questionData->question_text : 'Question text not available';
            
            // Build student responses map
            $studentResponsesMap = [];
            $correctCount = 0;
            $totalResponses = $questionResponses->count();
            
            foreach ($questionResponses as $response) {
                $studentResponsesMap[$response->student_id] = [
                    'answer' => $response->student_answer,
                    'is_correct' => $response->is_correct
                ];
                
                if ($response->is_correct) {
                    $correctCount++;
                }
            }
            
            // Calculate percentage
            $percentage = $totalResponses > 0 ? ($correctCount / $totalResponses) * 100 : 0;
            
            $itemAnalysis[] = [
                'item_number' => $itemNumber++,
                'question_id' => $questionId,
                'question' => $questionText,
                'student_responses' => $studentResponsesMap,
                'total_responses' => $totalResponses,
                'correct_responses' => $correctCount,
                'percentage' => $percentage
            ];
        }

        return view('admin.teacher.assessments.item-analysis', compact(
            'assessment',
            'sections',
            'totalExaminees',
            'itemAnalysis',
            'students'
        ));
    }
}
