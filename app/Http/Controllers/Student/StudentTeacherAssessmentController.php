<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAssignment;
use App\Models\QuizResult;
use App\Models\TeacherAssessmentSession;
use App\Models\TeacherQuizResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentTeacherAssessmentController extends Controller
{
    /**
     * Display a listing of teacher assessments for the student
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        
        // Get student's section from student_profile
        $studentProfile = DB::table('student_profile')->where('user_id', $student->id)->first();
        $studentSection = $studentProfile ? $studentProfile->section : null;
        
        // Get assessments assigned to this student
        $assignments = AssessmentAssignment::where(function($query) use ($student, $studentSection) {
            // First check for assignments specifically to this student
            $query->where('student_id', $student->id)
                  // Then check for section-wide assignments (where student_id is null)
                  ->orWhere(function($q) use ($studentSection) {
                      // Handle both "A" and "Section A" formats
                      $q->where(function($subQ) use ($studentSection) {
                          $subQ->where('section', $studentSection)
                               ->orWhere('section', 'Section ' . $studentSection);
                      })
                      ->whereNull('student_id'); // Only section-wide assignments
                  });
        })
        ->with(['assessment' => function($query) {
            $query->where('status', 'Active');
        }])
        ->whereHas('assessment', function($query) {
            $query->where('status', 'Active');
        })
        ->get();

        // Check for completed assessments and update status in memory
        $completedAssessmentIds = QuizResult::where('student_id', $student->id)
            ->pluck('assessment_id')
            ->toArray();

        foreach ($assignments as $assignment) {
            if (in_array($assignment->assessment_id, $completedAssessmentIds)) {
                $assignment->status = 'Completed';
            }
        }

        return view('student.assessments.index', compact('assignments'));
    }

    /**
     * Display the specified teacher assessment
     */
    public function show(Assessment $assessment)
    {
        // Check if student has access to this assessment
        $student = Auth::guard('student')->user();
        
        // Get student's section from student_profile
        $studentProfile = DB::table('student_profile')->where('user_id', $student->id)->first();
        $studentSection = $studentProfile ? $studentProfile->section : null;
        
        $assignment = AssessmentAssignment::where('assessment_id', $assessment->id)
            ->where(function($query) use ($student, $studentSection) {
                // First check for assignments specifically to this student
                $query->where('student_id', $student->id)
                      // Then check for section-wide assignments (where student_id is null)
                      ->orWhere(function($q) use ($studentSection) {
                          // Handle both "A" and "Section A" formats
                          $q->where(function($subQ) use ($studentSection) {
                              $subQ->where('section', $studentSection)
                                   ->orWhere('section', 'Section ' . $studentSection);
                          })
                          ->whereNull('student_id'); // Only section-wide assignments
                      });
            })
            ->first();

        if (!$assignment) {
            abort(403, 'You do not have access to this assessment.');
        }

        // Check if student has already completed this assessment
        $quizResult = QuizResult::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->first();

        // Helper to check for completed assessments
        if ($quizResult) {
            $assignment->status = 'Completed';
        }

        // Get past attempts history
        // We use TeacherAssessmentSession because QuizResult only stores the latest active result
        $history = TeacherAssessmentSession::where('user_id', $student->id)
            ->where('teacher_assessment_id', $assessment->id)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->get()
            ->map(function ($session) {
                // Calculate time taken matching teacher view logic
                $timeTaken = '-';
                if ($session->completed_at && $session->started_at) {
                    $diff = $session->started_at->diff($session->completed_at);
                    $timeTaken = sprintf('%02d:%02d', $diff->i + ($diff->h * 60), $diff->s);
                }

                return (object) [
                    'id' => $session->session_id,
                    'completed_at' => $session->completed_at,
                    'score' => $session->correct_answers,
                    'total_questions' => $session->total_questions,
                    'percentage' => $session->accuracy_percentage,
                    'time_taken' => $timeTaken
                ];
            });

        // Ensure Top Banner uses the same accurate time format as history
        if ($quizResult && $history->isNotEmpty()) {
            // Use the latest attempt's time
            $quizResult->formatted_time = $history->first()->time_taken;
        }

        return view('student.assessments.show', compact('assessment', 'assignment', 'quizResult', 'history'));
    }

    /**
     * Start the quiz for a teacher assessment
     */
    public function startQuiz(Assessment $assessment)
    {
        $student = Auth::guard('student')->user();
        
        // Check if student has access to this assessment
        $studentProfile = DB::table('student_profile')->where('user_id', $student->id)->first();
        $studentSection = $studentProfile ? $studentProfile->section : null;
        
        $assignment = AssessmentAssignment::where('assessment_id', $assessment->id)
            ->where(function($query) use ($student, $studentSection) {
                $query->where('student_id', $student->id)
                      ->orWhere(function($q) use ($studentSection) {
                          $q->where(function($subQ) use ($studentSection) {
                              $subQ->where('section', $studentSection)
                                   ->orWhere('section', 'Section ' . $studentSection);
                          })
                          ->whereNull('student_id');
                      });
            })
            ->first();

        if (!$assignment) {
            abort(403, 'You do not have access to this assessment.');
        }
        
        // Check if assessment is already completed
        if ($assignment->status === 'Completed') {
            return redirect()->route('teacher-assessments.show', $assessment->id)
                ->with('info', 'This assessment has already been completed.');
        }
        
        // Check for existing active session
        $existingSession = TeacherAssessmentSession::where('teacher_assessment_id', $assessment->id)
            ->where('user_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingSession) {
            // Resume session - get shuffled questions from saved session
            $questionDetails = $existingSession->questions_json;
            $sessionId = $existingSession->session_id;
            
            // Store quiz session data in Laravel session
            session([
                'quiz_assessment_id' => $assessment->id,
                'quiz_session_id' => $sessionId,
                'quiz_questions' => $questionDetails,
                'current_question_index' => $existingSession->current_question_index,
                'quiz_start_time' => $existingSession->started_at,
                'current_bkt_probability' => $existingSession->final_bkt_probability ?? 0.5 // Use stored probability if available
            ]);
            
            // Re-populate answered questions for frontend
            // We need to pass this to the view so the frontend knows which questions are answered
            // The frontend uses sessionStorage 'answeredQuestions', we might need to seed it
            
            // Get answered question IDs to restore frontend state
            $answeredQuestionIds = TeacherQuizResponse::where('session_id', $sessionId)
                ->pluck('question_id')
                ->toArray();
            
            $currentQuestionIndex = $existingSession->current_question_index;
            
            return view('student.assessments.quiz', compact('assessment', 'assignment', 'questionDetails', 'answeredQuestionIds', 'currentQuestionIndex'));
        }

        // Get questions for this assessment
        $questions = DB::table('teacher_assessment_questions')
            ->where('teacher_assessment_id', $assessment->id)
            ->orderBy('question_order')
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->route('teacher-assessments.show', $assessment->id)
                ->withErrors(['error' => 'No questions found for this assessment.']);
        }

        // Load question details from JSON files or database
        $unshuffledQuestions = [];
        foreach ($questions as $question) {
            $questionDetail = $this->getQuestionDetails($question->question_id);
            if ($questionDetail) {
                $unshuffledQuestions[] = array_merge($questionDetail, [
                    'pool_id' => $question->pool_id,
                    'question_order' => $question->question_order,
                    'is_answered' => $question->is_answered
                ]);
            }
        }

        // Apply Fisher-Yates shuffle to randomize questions
        $questionDetails = $this->fisherYatesShuffle($unshuffledQuestions);

        // Create teacher assessment session
        $sessionId = 'TAS-' . $student->id . '-' . $assessment->id . '-' . time();
        
        // Determine competency from assessment category
        $competencyMap = [
            'Number & Algebra' => 'number_algebra',
            'Measurement & Geometry' => 'measurement_geometry',
            'Data & Probability' => 'data_probability'
        ];
        $competency = $competencyMap[$assessment->category] ?? 'mixed';
        
        // Determine difficulty level
        $difficultyMap = [
            'Easy' => 'beginner',
            'Medium' => 'intermediate',
            'Hard' => 'advanced',
            'Mixed' => 'mixed'
        ];
        $difficultyLevel = $difficultyMap[$assessment->difficulty] ?? 'mixed';

        $session = TeacherAssessmentSession::create([
            'session_id' => $sessionId,
            'user_id' => $student->id,
            'teacher_assessment_id' => $assessment->id,
            'session_type' => 'teacher_created',
            'competency' => $competency,
            'difficulty_level' => $difficultyLevel,
            'total_questions' => count($questionDetails),
            'questions_json' => $questionDetails,
            'current_question_index' => 0,
            'time_limit_minutes' => $assessment->time_limit,
            'total_time_allowed_seconds' => $assessment->time_limit * 60,
            'countdown_started_at' => now(),
            'countdown_expires_at' => now()->addMinutes($assessment->time_limit),
            'time_remaining_seconds' => $assessment->time_limit * 60,
            'initial_bkt_probability' => 0.5, // Starting probability
            'status' => 'in_progress',
            'started_at' => now()
        ]);

        // Store quiz session data in Laravel session
        session([
            'quiz_assessment_id' => $assessment->id,
            'quiz_session_id' => $sessionId,
            'quiz_questions' => $questionDetails,
            'current_question_index' => 0,
            'quiz_start_time' => now(),
            'current_bkt_probability' => 0.5
        ]);
        
        $answeredQuestionIds = [];
        $currentQuestionIndex = 0;
        
        return view('student.assessments.quiz', compact('assessment', 'assignment', 'questionDetails', 'answeredQuestionIds', 'currentQuestionIndex'));
    }

    /**
     * Submit an answer for a question
     */
    public function submitAnswer(Request $request, Assessment $assessment)
    {
        $request->validate([
            'question_id' => 'required|string',
            'answer' => 'required|string',
            'time_taken' => 'required|integer|min:1'
        ]);

        $student = Auth::guard('student')->user();
        
        // Verify quiz session
        $sessionId = session('quiz_session_id');
        if (!$sessionId || session('quiz_assessment_id') != $assessment->id) {
            return response()->json(['success' => false, 'message' => 'Invalid quiz session.']);
        }

        // Get session from database
        $session = TeacherAssessmentSession::where('session_id', $sessionId)->first();
        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Session not found.']);
        }

        $questions = session('quiz_questions', []);
        $currentIndex = session('current_question_index', 0);

        // Find the question
        $question = null;
        foreach ($questions as $q) {
            if ($q['question_id'] === $request->question_id) {
                $question = $q;
                break;
            }
        }

        if (!$question) {
            return response()->json(['success' => false, 'message' => 'Question not found.']);
        }

        // Check if answer is correct
        $isCorrect = $request->answer === $question['correct_answer'];

        // Get current BKT probability
        $currentBKT = session('current_bkt_probability', 0.5);
        
        // Calculate new BKT probability
        $newBKT = $session->updateBKTProbability($isCorrect, $currentBKT);
        
        // Determine difficulty level from question_id
        $difficultyMap = [
            'B' => 'beginner',
            'I' => 'intermediate',
            'A' => 'advanced'
        ];
        preg_match('/^(NA|MG|DP)-(B|I|A)-(\d{3})$/', $question['question_id'], $matches);
        $difficultyLevel = $difficultyMap[$matches[2]] ?? 'intermediate';
        
        // Calculate time limit for this difficulty
        $timeLimits = [
            'beginner' => 30,
            'intermediate' => 45,
            'advanced' => 60
        ];
        $timeLimit = $timeLimits[$difficultyLevel];
        
        // Calculate points
        $responseModel = new TeacherQuizResponse();
        $points = $responseModel->calculatePoints($isCorrect, $request->time_taken, $timeLimit, $difficultyLevel);

        // Save response to database
        TeacherQuizResponse::create([
            'session_id' => $sessionId,
            'pool_id' => $question['pool_id'],
            'question_id' => $request->question_id,
            'student_id' => $student->id,
            'student_answer' => $request->answer,
            'correct_answer' => $question['correct_answer'],
            'is_correct' => $isCorrect,
            'points_earned' => $points,
            'time_taken' => $request->time_taken,
            'bkt_probability_before' => $currentBKT,
            'bkt_probability_after' => $newBKT,
            'difficulty_level' => $difficultyLevel,
            'competency' => $session->competency,
            'topic_tag' => $question['topic_tag'] ?? null,
            'answered_at' => now()
        ]);

        // Update session
        $session->increment('questions_answered');
        if ($isCorrect) {
            $session->increment('correct_answers');
            $session->increment('total_points_earned', $points);
        } else {
            $session->increment('incorrect_answers');
        }
        $session->current_question_index = $currentIndex + 1;
        $session->save();

        // Update Laravel session
        session([
            'current_question_index' => $currentIndex + 1,
            'current_bkt_probability' => $newBKT
        ]);
        
        // Debug logging
        Log::info('Answer submitted', [
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'question_id' => $request->question_id,
            'is_correct' => $isCorrect,
            'points' => $points,
            'bkt_before' => $currentBKT,
            'bkt_after' => $newBKT
        ]);

        // Move to next question
        $nextIndex = $currentIndex + 1;

        $isLastQuestion = $nextIndex >= count($questions);

        return response()->json([
            'success' => true,
            'is_correct' => $isCorrect,
            'is_last_question' => $isLastQuestion,
            'next_question_index' => $nextIndex
        ]);
    }

    /**
     * Complete the quiz
     */
    public function completeQuiz(Request $request, Assessment $assessment)
    {
        $student = Auth::guard('student')->user();

        // Verify quiz session
        $sessionId = session('quiz_session_id');
        if (!$sessionId || session('quiz_assessment_id') != $assessment->id) {
            return redirect()->route('teacher-assessments.show', $assessment->id)
                ->withErrors(['error' => 'Invalid quiz session.']);
        }

        // Get session from database
        $session = TeacherAssessmentSession::where('session_id', $sessionId)->first();
        if (!$session) {
            return redirect()->route('teacher-assessments.show', $assessment->id)
                ->withErrors(['error' => 'Session not found.']);
        }

        // Calculate total time from sum of response times (active time)
        $totalTime = now()->diffInSeconds($session->started_at);

        // Calculate accuracy percentage
        $accuracyPercentage = $session->total_questions > 0 
            ? round(($session->correct_answers / $session->total_questions) * 100, 2) 
            : 0;

        // Calculate average response time
        $avgResponseTime = $session->questions_answered > 0
            ? round($totalTime / $session->questions_answered, 3)
            : 0;

        // Get final BKT probability from session
        $finalBKT = session('current_bkt_probability', 0.5);

        // Calculate final mastery score
        $finalMasteryScore = $session->calculateFinalMasteryScore();

        // Update session with final metrics
        $session->update([
            'accuracy_percentage' => $accuracyPercentage,
            'average_response_time' => $avgResponseTime,
            'final_bkt_probability' => $finalBKT,
            'final_mastery_score' => $finalMasteryScore,
            'status' => 'completed',
            'completed_at' => now()
        ]);

        // Store results in quiz_results table (without answers)
        QuizResult::updateOrCreate(
            [
                'student_id' => $student->id,
                'assessment_id' => $assessment->id,
            ],
            [
                'session_id' => $sessionId,
                'score' => $session->correct_answers,
                'total_questions' => $session->total_questions,
                'percentage' => $accuracyPercentage,
                'time_taken' => $totalTime,
                'completed_at' => now(),
            ]
        );

        // Update assignment status
        $studentProfile = DB::table('student_profile')
            ->where('user_id', $student->id)
            ->first();

        $studentSection = $studentProfile ? $studentProfile->section : null;

        // IN COMPLETION METHOD - Replace your current query:
        $assignment = AssessmentAssignment::where('assessment_id', $assessment->id)
        ->where(function ($query) use ($student, $studentSection) {
            $query->where('student_id', $student->id)
                ->orWhere(function ($sectionQuery) use ($studentSection) {
                    $sectionQuery->where(function ($nameQuery) use ($studentSection) {
                        $nameQuery->where('section', $studentSection)
                                    ->orWhere('section', 'Section ' . $studentSection);
                    })
                    ->whereNull('student_id');
                });
        })
        ->orderByRaw('CASE WHEN student_id IS NOT NULL THEN 0 ELSE 1 END') // Student-specific first
        ->first(); // Use first() instead of get()

        if ($assignment) {
            Log::info('Updating assignment status after quiz completion', [
                'assignment_id' => $assignment->id,
                'student_id' => $assignment->student_id,
                'section' => $assignment->section,
                'from_status' => $assignment->status,
                'assessment_id' => $assessment->id,
            ]);
            $assignment->update(['status' => 'Completed']);
        }

        if (!$assignment) {
            Log::warning('No assignment record matched for completion update', [
                'student_id' => $student->id,
                'section' => $studentSection,
                'assessment_id' => $assessment->id,
            ]);
        }

        // Check if all students have completed this assessment
        $this->checkAssessmentCompletion($assessment);

        // Clear quiz session
        session()->forget([
            'quiz_assessment_id', 'quiz_session_id', 'quiz_questions', 'current_question_index',
            'quiz_start_time', 'current_bkt_probability'
        ]);

        // Redirect with success message
        return redirect()->route('teacher-assessments.show', $assessment->id)
            ->with('success', "Quiz completed! Score: {$session->correct_answers}/{$session->total_questions} ({$accuracyPercentage}%)");
    }

    /**
     * Check if all assigned students have completed the assessment
     * If yes, mark the assessment as Completed
     */
    private function checkAssessmentCompletion(Assessment $assessment)
    {
        // Get total number of assignments for this assessment
        $totalAssignments = AssessmentAssignment::where('assessment_id', $assessment->id)->count();
        
        // Get number of completed assignments
        $completedAssignments = AssessmentAssignment::where('assessment_id', $assessment->id)
            ->where('status', 'Completed')
            ->count();
        
        Log::info('Checking assessment completion', [
            'assessment_id' => $assessment->id,
            'total_assignments' => $totalAssignments,
            'completed_assignments' => $completedAssignments,
            'current_status' => $assessment->status
        ]);
        
        // If all assignments are completed and assessment is Active, mark it as Completed
        if ($totalAssignments > 0 && $completedAssignments === $totalAssignments && $assessment->status === 'Active') {
            $assessment->status = 'Completed';
            $assessment->save();
            
            Log::info('Assessment marked as Completed - all students finished', [
                'assessment_id' => $assessment->id,
                'total_students' => $totalAssignments
            ]);
        }
    }

    /**
     * Allow student to retake a completed assessment
     */
    public function retakeQuiz(Assessment $assessment)
    {
        $student = Auth::guard('student')->user();
        Log::info('Retake requested', [
            'student_id' => $student ? $student->id : null,
            'assessment_id' => $assessment->id,
        ]);

        // Verify student access
        $studentProfile = DB::table('student_profile')->where('user_id', $student->id)->first();
        $studentSection = $studentProfile ? $studentProfile->section : null;

        // IN RETAKE METHOD - Replace your current query:
        $assignment = AssessmentAssignment::where('assessment_id', $assessment->id)
        ->where(function($query) use ($student, $studentSection) {
            $query->where('student_id', $student->id)
                ->orWhere(function($q) use ($studentSection) {
                    $q->where(function($subQ) use ($studentSection) {
                        $subQ->where('section', $studentSection)
                            ->orWhere('section', 'Section ' . $studentSection);
                    })
                    ->whereNull('student_id');
                });
        })
        ->orderByRaw('CASE WHEN student_id IS NOT NULL THEN 0 ELSE 1 END') // Student-specific first
        ->first();

        if (!$assignment) {
            Log::warning('Retake blocked: no assignment record', [
                'student_id' => $student->id,
                'assessment_id' => $assessment->id,
                'section' => $studentSection,
            ]);
            abort(403, 'You do not have access to this assessment.');
        }

        if ($assignment->status !== 'Completed') {
            Log::info('Retake blocked: assignment not completed', [
                'assignment_id' => $assignment->id,
                'current_status' => $assignment->status,
            ]);
            return redirect()->route('teacher-assessments.show', $assessment->id)
                ->with('info', 'This assessment has not been completed yet.');
        }

        // Archive the old completed session
        $oldSession = TeacherAssessmentSession::where('teacher_assessment_id', $assessment->id)
            ->where('user_id', $student->id)
            ->where('status', 'completed')
            ->latest()
            ->first();

        if ($oldSession) {
            Log::info('Archiving old session before retake', [
                'session_id' => $oldSession->session_id,
                'status' => $oldSession->status,
            ]);
            $oldSession->update([
                'status' => 'completed',
                'session_notes' => 'Archived for retake on ' . now()->toDateTimeString(),
            ]);
        } else {
            Log::warning('No previous session found to archive for retake', [
                'student_id' => $student->id,
                'assessment_id' => $assessment->id,
            ]);
        }

        // Reset assignment status to allow a new attempt
        $assignment->status = 'Assigned';
        $assignment->save();
        Log::info('Assignment reopened for retake', [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
        ]);

        // Delete previous quiz result so the student can start fresh
        QuizResult::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->delete();

        // Clear any stored quiz session data
        session()->forget([
            'quiz_assessment_id',
            'quiz_session_id',
            'quiz_questions',
            'current_question_index',
            'quiz_start_time',
            'current_bkt_probability',
            'quiz_answers',
        ]);

        Log::info('Retake flow completed, redirecting to quiz start', [
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
        ]);

        return redirect()->route('teacher-assessments.start', $assessment->id)
            ->with('success', 'Assessment reset! You can now retake the quiz.');
    }

    /**
     * Show detailed results for a completed assessment
     */
    public function results(Assessment $assessment)
    {
        $student = Auth::guard('student')->user();

        // Check if a specific session ID is requested
        if (request()->has('session_id')) {
            $session = TeacherAssessmentSession::where('teacher_assessment_id', $assessment->id)
                ->where('user_id', $student->id)
                ->where('session_id', request('session_id'))
                ->where('status', 'completed')
                ->first();
        } else {
            // Get the latest completed session
            $session = TeacherAssessmentSession::where('teacher_assessment_id', $assessment->id)
                ->where('user_id', $student->id)
                ->where('status', 'completed')
                ->latest()
                ->first();
        }

        if (!$session) {
            return redirect()->route('teacher-assessments.show', $assessment->id)
                ->with('error', 'No completed session found for this assessment.');
        }

        // Get quiz result (Note: QuizResult might only track latest, so we rely on session for historical data)
        // If viewing an old session, QuizResult might not match. 
        // We'll trust the session data for score/percentage which we pass to view.
        $quizResult = QuizResult::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->first();

        // If viewing history, we might want to construct a temporary result object from session
        if (request()->has('session_id')) {
             $quizResult = (object)[
                'score' => $session->correct_answers,
                'total_questions' => $session->total_questions,
                'percentage' => $session->accuracy_percentage,
                'time_taken' => \Carbon\Carbon::parse($session->completed_at)->diffInSeconds(\Carbon\Carbon::parse($session->started_at))
             ];
        }

        // Get detailed responses with question text
        $responses = TeacherQuizResponse::where('session_id', $session->session_id)
            ->orderBy('created_at')
            ->get();

        // Fetch question details for each response
        $detailedResponses = [];
        foreach ($responses as $response) {
            $questionDetail = $this->getQuestionDetails($response->question_id);
            if ($questionDetail) {
                $detailedResponses[] = array_merge($response->toArray(), [
                    'question_text' => $questionDetail['question_text'], // Corrected key from 'question' to 'question_text'
                    'choices' => [ // Assuming choices are structured like this in getQuestionDetails
                        'a' => $questionDetail['choice_a'] ?? null,
                        'b' => $questionDetail['choice_b'] ?? null,
                        'c' => $questionDetail['choice_c'] ?? null,
                        'd' => $questionDetail['choice_d'] ?? null,
                    ],
                    'correct_answer_text' => $questionDetail['correct_answer'],
                    'explanation' => $questionDetail['explanation'] ?? null
                ]);
            }
        }

        return view('student.assessments.results', compact('assessment', 'session', 'quizResult', 'detailedResponses'));
    }

    /**
     * Get question details from JSON files or database
     */
    private function getQuestionDetails($questionId)
    {
        // Check JSON files for bank questions
        $categoryMap = [
            'NA' => 'number_algebra',
            'MG' => 'measurement_geometry', 
            'DP' => 'data_probability'
        ];

        $difficultyMap = [
            'B' => 'beginner',
            'I' => 'intermediate',
            'A' => 'advanced'
        ];

        // Parse question ID (e.g., NA-B-001)
        if (preg_match('/^(NA|MG|DP)-(B|I|A)-(\d{3})$/', $questionId, $matches)) {
            $categoryPrefix = $categoryMap[$matches[1]];
            $difficulty = $difficultyMap[$matches[2]];
            
            $filePath = base_path("database/data/{$categoryPrefix}/{$categoryPrefix}_{$difficulty}.json");
            
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $questions = json_decode($content, true);
                
                if ($questions) {
                    foreach ($questions as $question) {
                        if ($question['question_id'] === $questionId) {
                            return [
                                'question_id' => $question['question_id'],
                                'question_text' => $question['question_text'],
                                'question_type' => $question['question_type'],
                                'choice_a' => $question['choice_a'] ?? '',
                                'choice_b' => $question['choice_b'] ?? '',
                                'choice_c' => $question['choice_c'] ?? '',
                                'choice_d' => $question['choice_d'] ?? '',
                                'correct_answer' => $question['correct_answer'],
                                'hint_text' => $question['hint_text'] ?? '',
                                'explanation' => $question['explanation'] ?? '',
                                'topic_tag' => $question['topic_tag'] ?? null
                            ];
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Fisher-Yates shuffle algorithm for randomizing questions
     */
    private function fisherYatesShuffle($array)
    {
        $count = count($array);
        for ($i = $count - 1; $i > 0; $i--) {
            $j = rand(0, $i);
            // Swap elements
            $temp = $array[$i];
            $array[$i] = $array[$j];
            $array[$j] = $temp;
        }
        return $array;
    }
}

