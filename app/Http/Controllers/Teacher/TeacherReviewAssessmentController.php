<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherReviewAssessmentController extends Controller
{
    /**
     * Show assessment review page for a specific student and assessment
     */
    public function reviewAssessment($student, $assessment)
    {
        try {
            $studentId = $student;
            $assessmentId = $assessment;
            
            // Get student information
            $student = DB::table('users')
                ->join('student_profile', 'users.id', '=', 'student_profile.user_id')
                ->where('users.id', $studentId)
                ->where('users.role', 'Student')
                ->select([
                    'users.id',
                    'users.username',
                    'users.email',
                    'student_profile.firstname',
                    'student_profile.lastname',
                    'student_profile.section',
                    'student_profile.grade_level',
                    'student_profile.student_id',
                    'student_profile.total_points'
                ])
                ->first();
            
            if (!$student) {
                return redirect()->back()->with('error', 'Student not found.');
            }
            
            // Add full name to student object
            $student->name = $student->firstname . ' ' . $student->lastname;
            
            // Get assessment details - could be from assessments or diagnostic_sessions table
            $assessment = DB::table('assessments')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $studentId)
                ->first();
            
            $assessmentType = 'regular';
            $completedAt = null;
            $competency = null;
            
            if (!$assessment) {
                // Check if it's a diagnostic session
                $diagnosticSession = DB::table('diagnostic_sessions')
                    ->where('session_id', $assessmentId)
                    ->where('user_id', $studentId)
                    ->first();
                
                if ($diagnosticSession) {
                    $assessmentType = 'diagnostic';
                    $completedAt = $diagnosticSession->completed_at ?? $diagnosticSession->started_at;
                    $competency = $diagnosticSession->competency;
                } else {
                    return redirect()->back()->with('error', 'Assessment not found.');
                }
            } else {
                $completedAt = $assessment->completed_at;
                $competency = $assessment->competency;
            }
            
            // Get question responses with question details
            $questionResponses = DB::table('question_responses')
                ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
                ->where('question_responses.assessment_id', $assessmentId)
                ->select([
                    'question_responses.*',
                    'questions.question_text',
                    'questions.question_type',
                    'questions.choice_a',
                    'questions.choice_b',
                    'questions.choice_c',
                    'questions.choice_d',
                    'questions.correct_answer',
                    'questions.explanation',
                    'questions.difficulty_level',
                    'questions.topic_tag as topic'
                ])
                ->orderBy('question_responses.answered_at')
                ->get();
            
            if ($questionResponses->isEmpty()) {
                return redirect()->back()->with('error', 'No question responses found for this assessment.');
            }
            
            // Calculate statistics
            $totalQuestions = $questionResponses->count();
            $correctAnswers = $questionResponses->where('is_correct', 1)->count();
            $wrongAnswers = $totalQuestions - $correctAnswers;
            $scorePercentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
            
            // Calculate time statistics
            $totalTimeSeconds = $questionResponses->sum('response_time'); // Using response_time column
            $totalTimeMinutes = floor($totalTimeSeconds / 60);
            $totalTimeRemainingSeconds = $totalTimeSeconds % 60;
            $totalTimeFormatted = $totalTimeMinutes . ' min ' . $totalTimeRemainingSeconds . ' sec';
            $avgTimePerQuestion = $totalQuestions > 0 ? round($totalTimeSeconds / $totalQuestions) : 0;
            
            // Format questions for the view
            $formattedQuestions = $questionResponses->map(function($response) {
                return [
                    'question_text' => $response->question_text,
                    'is_correct' => $response->is_correct,
                    'category' => ucfirst(str_replace('_', ' ', $response->topic ?? 'General')),
                    'time_spent' => round($response->response_time) . ' sec', // Using response_time
                    'options' => [
                        'A' => $response->choice_a,
                        'B' => $response->choice_b,
                        'C' => $response->choice_c,
                        'D' => $response->choice_d,
                    ],
                    'student_answer' => $response->user_answer, // Changed from selected_answer to user_answer
                    'correct_answer' => $response->correct_answer,
                    'explanation' => $response->explanation
                ];
            });
            
            // Analyze performance by topic
            $topicPerformance = $questionResponses->groupBy('topic')->map(function($responses, $topic) {
                $total = $responses->count();
                $correct = $responses->where('is_correct', 1)->count();
                $percentage = $total > 0 ? ($correct / $total) * 100 : 0;
                
                return [
                    'topic' => ucfirst(str_replace('_', ' ', $topic ?? 'General')),
                    'percentage' => round($percentage),
                    'correct' => $correct,
                    'total' => $total
                ];
            });
            
            // Identify strong areas (>80%) and areas for improvement (<70%)
            $strongAreas = $topicPerformance->filter(function($perf) {
                return $perf['percentage'] >= 80;
            })->pluck('topic')->implode(', ');
            
            $improvementAreas = $topicPerformance->filter(function($perf) {
                return $perf['percentage'] < 70;
            })->pluck('topic')->implode(', ');
            
            // Generate personalized recommendation
            $recommendation = $this->generateRecommendation(
                $scorePercentage, 
                $avgTimePerQuestion, 
                $improvementAreas,
                $student->name
            );
            
            // Create result object for the view
            $result = (object) [
                'student_id' => $student->student_id ?? $student->id,
                'score' => $scorePercentage,
                'accuracy' => $scorePercentage,
                'correct_count' => $correctAnswers,
                'wrong_count' => $wrongAnswers,
                'total_questions' => $totalQuestions,
                'correct_rate' => $scorePercentage,
                'wrong_rate' => $totalQuestions > 0 ? round(($wrongAnswers / $totalQuestions) * 100) : 0,
                'time_spent' => $totalTimeFormatted,
                'avg_time' => $avgTimePerQuestion,
                'completed_at' => $completedAt ? date('Y-m-d', strtotime($completedAt)) : date('Y-m-d')
            ];
            
            // Create assessment object
            $assessmentTitle = $assessmentType === 'diagnostic' 
                ? ucfirst(str_replace('_', ' & ', $competency)) . ' Diagnostic Assessment'
                : (isset($assessment->title) ? $assessment->title : 'Assessment');
            
            $assessmentObj = (object) [
                'title' => $assessmentTitle,
                'subject' => ucfirst(str_replace('_', ' & ', $competency))
            ];
            
            // Create summary object
            $summary = (object) [
                'strong_areas' => $strongAreas ?: 'Continue practicing to identify strengths',
                'improvement_areas' => $improvementAreas ?: 'Great performance across all topics!',
                'recommendation' => $recommendation
            ];
            
            return view('admin.teacher.sections.review-assessment', [
                'student' => $student,
                'result' => $result,
                'assessment' => $assessmentObj,
                'questions' => $formattedQuestions,
                'summary' => $summary,
                'assessmentType' => $assessmentType
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in reviewAssessment: ' . $e->getMessage(), [
                'student_id' => $student,
                'assessment_id' => $assessment,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'An error occurred while loading the assessment review.');
        }
    }
    
    /**
     * Generate personalized recommendation based on student performance
     */
    private function generateRecommendation($scorePercentage, $avgTimePerQuestion, $improvementAreas, $studentName)
    {
        $recommendations = [];
        
        // Performance-based recommendation
        if ($scorePercentage >= 95) {
            $recommendations[] = "{$studentName} demonstrates exceptional mastery of the concepts with outstanding accuracy.";
        } elseif ($scorePercentage >= 85) {
            $recommendations[] = "{$studentName} shows strong understanding of the material with very good performance.";
        } elseif ($scorePercentage >= 75) {
            $recommendations[] = "{$studentName} demonstrates solid grasp of most concepts but has room for improvement.";
        } elseif ($scorePercentage >= 60) {
            $recommendations[] = "{$studentName} shows developing understanding but needs additional practice and support.";
        } else {
            $recommendations[] = "{$studentName} requires significant support and intervention to build foundational understanding.";
        }
        
        // Time management feedback
        if ($avgTimePerQuestion < 20) {
            $recommendations[] = "The student works very quickly. Encourage them to double-check their work to minimize careless errors.";
        } elseif ($avgTimePerQuestion < 40) {
            $recommendations[] = "Time management is good with efficient problem-solving pace.";
        } elseif ($avgTimePerQuestion < 60) {
            $recommendations[] = "The student takes adequate time per question. Consider strategies to improve efficiency while maintaining accuracy.";
        } else {
            $recommendations[] = "The student may benefit from time management strategies and additional practice to build confidence and speed.";
        }
        
        // Areas for improvement
        if (!empty($improvementAreas)) {
            $recommendations[] = "Recommend focused review and additional practice on: " . $improvementAreas . ".";
            $recommendations[] = "Consider providing supplementary materials, one-on-one tutoring, or small group instruction for these topics.";
        } else {
            $recommendations[] = "The student performed well across all topic areas. Continue to challenge them with advanced problems.";
        }
        
        return implode(' ', $recommendations);
    }
    
    /**
     * Get all assessments for a student
     */
    public function getStudentAssessments($studentId)
    {
        try {
            // Get regular assessments
            $regularAssessments = DB::table('assessments')
                ->where('user_id', $studentId)
                ->where('status', 'completed')
                ->select([
                    'assessment_id as id',
                    'title as name',
                    'completed_at as date',
                    'total_time_taken as time',
                    'competency',
                    'score',
                    DB::raw("'Assessment' as type")
                ])
                ->get();
            
            // Get diagnostic sessions
            $diagnosticSessions = DB::table('diagnostic_sessions')
                ->where('user_id', $studentId)
                ->where('status', 'completed')
                ->select([
                    'session_id as id',
                    DB::raw("CONCAT(UPPER(SUBSTRING(competency, 1, 1)), SUBSTRING(competency, 2), ' Diagnostic') as name"),
                    'completed_at as date',
                    DB::raw("'N/A' as time"),
                    'competency',
                    DB::raw("NULL as score"),
                    DB::raw("'Diagnostic' as type")
                ])
                ->get();
            
            // Combine and format
            $allAssessments = $regularAssessments->concat($diagnosticSessions)
                ->map(function($assessment) use ($studentId) {
                    // Calculate score if not available
                    if (is_null($assessment->score)) {
                        $responses = DB::table('question_responses')
                            ->where('assessment_id', $assessment->id)
                            ->get();
                        
                        $total = $responses->count();
                        $correct = $responses->where('is_correct', 1)->count();
                        $assessment->score = $total > 0 ? round(($correct / $total) * 100) . '%' : 'N/A';
                    } else {
                        $assessment->score = round($assessment->score) . '%';
                    }
                    
                    // Format time
                    if ($assessment->time !== 'N/A' && is_numeric($assessment->time)) {
                        $minutes = floor($assessment->time / 60);
                        $seconds = $assessment->time % 60;
                        $assessment->time = $minutes . '.' . round($seconds / 60 * 10) . ' min';
                    }
                    
                    // Format date
                    $assessment->date = date('Y-m-d', strtotime($assessment->date));
                    
                    return $assessment;
                })
                ->sortByDesc('date')
                ->values();
            
            return $allAssessments;
            
        } catch (\Exception $e) {
            Log::error('Error getting student assessments: ' . $e->getMessage());
            return collect([]);
        }
    }
}