<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Show analytics dashboard
     */
    public function index()
    {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return redirect()->route('admin.login');
        }

        // Get current teacher's sections
        $teacherId = Auth::guard('admin')->user()->id;
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacherId)->first();
        $teacherSections = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->pluck('section')
            ->toArray();

        // Get recent assessments with real data (filtered by teacher sections)
        $recentAssessments = $this->getRecentAssessments($teacherSections);
        
        // Get overall statistics (filtered by teacher sections)
        $statistics = $this->getOverallStatistics($teacherSections);

        return view('admin.teacher.analytics.index', [
            'recentAssessments' => $recentAssessments,
            'statistics' => $statistics,
            'teacherSections' => $teacherSections
        ]);
    }

    /**
     * Show detailed assessment view
     */
    public function showAssessmentDetails(Request $request, $assessmentId)
    {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return redirect()->route('admin.login');
        }

        // Parse the assessment ID (format: competency_assessmenttype)
        $parts = explode('_', $assessmentId);
        if (count($parts) < 2) {
            return redirect()->route('teacher.analytics')
                ->with('error', 'Invalid assessment identifier.');
        }

        $competency = $parts[0] . '_' . $parts[1]; // Reconstruct competency (e.g., number_algebra)
        $assessmentType = $parts[2] ?? 'regular'; // Get assessment type

        // Get current teacher's sections
        $teacherId = Auth::guard('admin')->user()->id;
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacherId)->first();
        $teacherSections = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->pluck('section')
            ->toArray();

        // Get selected section from request (default to all sections)
        $selectedSection = $request->get('section', 'all');
        $sortBy = $request->get('sort', 'accuracy'); // accuracy, difficulty, time, bkt_improvement

        // Get assessment summary info
        $assessmentSummary = DB::table('assessments')
            ->select([
                'competency',
                'difficulty_level',
                DB::raw('COUNT(*) as total_completed'),
                DB::raw('MAX(completed_at) as latest_completed_at'),
                DB::raw('AVG(questions_answered) as avg_questions'),
                DB::raw('AVG(accuracy_percentage) as avg_accuracy')
            ])
            ->where('competency', $competency)
            ->where('difficulty_level', $assessmentType)
            ->where('assessment_type', 'regular') // Only regular assessments
            ->where('status', 'completed')
            ->groupBy('competency', 'difficulty_level')
            ->first();

        if (!$assessmentSummary) {
            return redirect()->route('teacher.analytics')
                ->with('error', 'Assessment not found.');
        }

        // Get detailed question statistics for this assessment type with section filtering
        $questionDetails = $this->getQuestionStatisticsForType($competency, $assessmentType, $selectedSection, $teacherSections);
        
        // Get overall assessment statistics
        $assessmentStats = $this->getAssessmentStatisticsForType($competency, $assessmentType, $selectedSection, $teacherSections);

        // Sort question details
        $questionDetails = $this->sortQuestionDetails($questionDetails, $sortBy);

        return view('admin.teacher.analytics.assessment-details', [
            'assessment' => $assessmentSummary,
            'questionDetails' => $questionDetails,
            'assessmentStats' => $assessmentStats,
            'competency' => $competency,
            'assessmentType' => $assessmentType,
            'teacherSections' => $teacherSections,
            'selectedSection' => $selectedSection,
            'sortBy' => $sortBy
        ]);
    }

    /**
     * Get recent assessments with real data
     */
    private function getRecentAssessments($teacherSections = [])
    {
        try {
            // Get unique assessment types (competency + difficulty combinations) with their latest completion date
            // Only include regular assessments, exclude diagnostics, filtered by teacher sections
            $results = DB::table('assessments')
                ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                ->select([
                    'assessments.competency',
                    'assessments.difficulty_level',
                    DB::raw('MAX(assessments.completed_at) as latest_completed_at'),
                    DB::raw('COUNT(*) as total_completed'),
                    DB::raw('SUM(assessments.questions_answered) as total_questions_answered')
                ])
                ->where('assessments.status', 'completed')
                ->where('assessments.assessment_type', 'regular') // Only regular assessments
                ->whereNotNull('assessments.completed_at')
                ->where('assessments.questions_answered', '>', 0)
                ->whereIn('student_profile.section', $teacherSections) // Filter by teacher sections
                ->groupBy('assessments.competency', 'assessments.difficulty_level')
                ->orderBy('latest_completed_at', 'desc')
                ->limit(10)
                ->get();
            
            $mapped = $results->map(function ($assessment) {
                // Format competency name for display
                $competencyNames = [
                    'number_algebra' => 'Number and Algebra',
                    'measurement_geometry' => 'Measurement and Geometry', 
                    'data_probability' => 'Data and Probability'
                ];
                
                $assessment->competency_display = $competencyNames[$assessment->competency] ?? ucfirst(str_replace('_', ' ', $assessment->competency));
                $assessment->assessment_name = $assessment->competency_display . ' ' . ucfirst($assessment->difficulty_level ?? 'Unknown');
                $assessment->formatted_date = Carbon::parse($assessment->latest_completed_at)->format('M d, Y');
                
                // Create a unique identifier for this assessment type
                $assessment->assessment_id = $assessment->competency . '_' . ($assessment->difficulty_level ?? 'unknown');
                
                return $assessment;
            });
            
            \Log::info('Mapped assessments: ', $mapped->toArray());
            return $mapped;
        } catch (\Exception $e) {
            // Log the error and return empty collection
            \Log::error('Error fetching recent assessments: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get overall statistics
     */
    private function getOverallStatistics($teacherSections = [])
    {
        try {
            $totalAssessments = DB::table('assessments')
                ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                ->where('assessments.status', 'completed')
                ->where('assessments.assessment_type', 'regular') // Only regular assessments
                ->whereIn('student_profile.section', $teacherSections) // Filter by teacher sections
                ->count();

            $totalStudents = DB::table('student_profile')
                ->whereIn('section', $teacherSections) // Only students in teacher's sections
                ->count();

            $avgAccuracy = DB::table('assessments')
                ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                ->where('assessments.status', 'completed')
                ->where('assessments.assessment_type', 'regular') // Only regular assessments
                ->whereIn('student_profile.section', $teacherSections) // Filter by teacher sections
                ->whereNotNull('assessments.accuracy_percentage')
                ->avg('assessments.accuracy_percentage');

            $completionRate = DB::table('assessments')
                ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                ->where('assessments.assessment_type', 'regular') // Only regular assessments
                ->whereIn('student_profile.section', $teacherSections) // Filter by teacher sections
                ->selectRaw('
                    COUNT(CASE WHEN assessments.status = "completed" THEN 1 END) as completed,
                    COUNT(*) as total
                ')
                ->first();

            return [
                'total_assessments' => $totalAssessments,
                'total_students' => $totalStudents,
                'avg_accuracy' => round($avgAccuracy, 1),
                'completion_rate' => $completionRate->total > 0 ? 
                    round(($completionRate->completed / $completionRate->total) * 100, 1) : 0
            ];
        } catch (\Exception $e) {
            // Log the error and return default values
            \Log::error('Error fetching overall statistics: ' . $e->getMessage());
            return [
                'total_assessments' => 0,
                'total_students' => 0,
                'avg_accuracy' => 0,
                'completion_rate' => 0
            ];
        }
    }

    /**
     * Get question-level statistics for a specific assessment type
     */
    private function getQuestionStatisticsForType($competency, $assessmentType, $selectedSection = 'all', $teacherSections = [])
    {
        try {
            // Build the base query
            $query = DB::table('question_responses')
                ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
                ->join('assessments', 'question_responses.assessment_id', '=', 'assessments.assessment_id')
                ->join('student_profile', 'question_responses.user_id', '=', 'student_profile.user_id')
                ->where('assessments.competency', $competency)
                ->where('assessments.difficulty_level', $assessmentType)
                ->where('assessments.assessment_type', 'regular') // Only regular assessments
                ->where('assessments.status', 'completed');

            // Filter by section if not 'all'
            if ($selectedSection !== 'all' && in_array($selectedSection, $teacherSections)) {
                $query->where('student_profile.section', $selectedSection);
            } else {
                // Only show sections that the teacher handles
                $query->whereIn('student_profile.section', $teacherSections);
            }

            $questionStats = $query
                ->select([
                    'questions.question_id',
                    'questions.question_text',
                    'questions.question_type',
                    'questions.choice_a',
                    'questions.choice_b',
                    'questions.choice_c',
                    'questions.choice_d',
                    'questions.correct_answer',
                    'questions.explanation',
                    'questions.difficulty_level',
                    'questions.topic_tag',
                    DB::raw('COUNT(*) as total_attempts'),
                    DB::raw('SUM(question_responses.is_correct) as correct_answers'),
                    DB::raw('AVG(question_responses.response_time) as avg_response_time'),
                    DB::raw('COUNT(DISTINCT question_responses.user_id) as unique_students'),
                    DB::raw('AVG(question_responses.bkt_before) as avg_bkt_before'),
                    DB::raw('AVG(question_responses.bkt_after) as avg_bkt_after'),
                    DB::raw('AVG(question_responses.time_score) as avg_time_score'),
                    DB::raw('AVG(question_responses.difficulty_factor) as avg_difficulty_factor')
                ])
                ->groupBy([
                    'questions.question_id',
                    'questions.question_text',
                    'questions.question_type',
                    'questions.choice_a',
                    'questions.choice_b',
                    'questions.choice_c',
                    'questions.choice_d',
                    'questions.correct_answer',
                    'questions.explanation',
                    'questions.difficulty_level',
                    'questions.topic_tag'
                ])
                ->orderBy('questions.difficulty_level')
                ->orderBy('questions.question_id')
                ->get();

            $results = [];
            foreach ($questionStats as $stat) {
                $results[$stat->question_id] = [
                    'question' => $stat,
                    'total_attempts' => $stat->total_attempts,
                    'correct_answers' => $stat->correct_answers,
                    'wrong_answers' => $stat->total_attempts - $stat->correct_answers,
                    'accuracy_rate' => $stat->total_attempts > 0 ? 
                        round(($stat->correct_answers / $stat->total_attempts) * 100, 1) : 0,
                    'avg_response_time' => round($stat->avg_response_time, 2),
                    'unique_students' => $stat->unique_students,
                    'avg_bkt_before' => round($stat->avg_bkt_before ?? 0, 4),
                    'avg_bkt_after' => round($stat->avg_bkt_after ?? 0, 4),
                    'bkt_improvement' => round(($stat->avg_bkt_after ?? 0) - ($stat->avg_bkt_before ?? 0), 4),
                    'avg_time_score' => round($stat->avg_time_score ?? 0, 2),
                    'avg_difficulty_factor' => round($stat->avg_difficulty_factor ?? 0, 2)
                ];
            }

            return $results;
        } catch (\Exception $e) {
            \Log::error('Error fetching question statistics: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get question-level statistics for an assessment (legacy method - keeping for compatibility)
     */
    private function getQuestionStatistics($assessmentId)
    {
        // Get all responses for this assessment with question details
        $responses = DB::table('question_responses')
            ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
            ->select([
                'questions.question_id',
                'questions.question_text',
                'questions.question_type',
                'questions.choice_a',
                'questions.choice_b',
                'questions.choice_c',
                'questions.choice_d',
                'questions.correct_answer',
                'questions.explanation',
                'questions.difficulty_level',
                'questions.topic_tag',
                'question_responses.user_answer',
                'question_responses.is_correct',
                'question_responses.response_time',
                'question_responses.total_points'
            ])
            ->where('question_responses.assessment_id', $assessmentId)
            ->orderBy('question_responses.answered_at')
            ->get();

        // Get statistics for each question across all students who took similar assessments
        $questionStats = [];
        foreach ($responses as $response) {
            $questionId = $response->question_id;
            
            // Get statistics for this question across all assessments
            $stats = DB::table('question_responses')
                ->join('assessments', 'question_responses.assessment_id', '=', 'assessments.assessment_id')
                ->where('question_responses.question_id', $questionId)
                ->where('assessments.status', 'completed')
                ->selectRaw('
                    COUNT(*) as total_attempts,
                    SUM(is_correct) as correct_answers,
                    AVG(response_time) as avg_response_time,
                    COUNT(DISTINCT question_responses.user_id) as unique_students
                ')
                ->first();

            $questionStats[$questionId] = [
                'question' => $response,
                'total_attempts' => $stats->total_attempts,
                'correct_answers' => $stats->correct_answers,
                'wrong_answers' => $stats->total_attempts - $stats->correct_answers,
                'accuracy_rate' => $stats->total_attempts > 0 ? 
                    round(($stats->correct_answers / $stats->total_attempts) * 100, 1) : 0,
                'avg_response_time' => round($stats->avg_response_time, 2),
                'unique_students' => $stats->unique_students,
                'current_student_correct' => $response->is_correct,
                'current_student_time' => $response->response_time,
                'current_student_answer' => $response->user_answer
            ];
        }

        return $questionStats;
    }

    /**
     * Get overall assessment statistics for a specific assessment type
     */
    private function getAssessmentStatisticsForType($competency, $assessmentType, $selectedSection = 'all', $teacherSections = [])
    {
        try {
            // Build base query for assessments
            $assessmentQuery = DB::table('assessments')
                ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                ->where('assessments.competency', $competency)
                ->where('assessments.difficulty_level', $assessmentType)
                ->where('assessments.assessment_type', 'regular') // Only regular assessments
                ->where('assessments.status', 'completed');

            // Filter by section if not 'all'
            if ($selectedSection !== 'all' && in_array($selectedSection, $teacherSections)) {
                $assessmentQuery->where('student_profile.section', $selectedSection);
            } else {
                // Only show sections that the teacher handles
                $assessmentQuery->whereIn('student_profile.section', $teacherSections);
            }

            // Get overall statistics for this assessment type
            $overallStats = $assessmentQuery
                ->selectRaw('
                    COUNT(*) as total_assessments,
                    AVG(assessments.questions_answered) as avg_questions,
                    AVG(assessments.correct_answers) as avg_correct,
                    AVG(assessments.accuracy_percentage) as avg_accuracy,
                    SUM(assessments.total_time_spent) as total_time,
                    AVG(assessments.average_response_time) as avg_response_time
                ')
                ->first();

            // Get difficulty breakdown with section filtering
            $difficultyQuery = DB::table('question_responses')
                ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
                ->join('assessments', 'question_responses.assessment_id', '=', 'assessments.assessment_id')
                ->join('student_profile', 'question_responses.user_id', '=', 'student_profile.user_id')
                ->where('assessments.competency', $competency)
                ->where('assessments.difficulty_level', $assessmentType)
                ->where('assessments.assessment_type', 'regular') // Only regular assessments
                ->where('assessments.status', 'completed');

            // Filter by section if not 'all'
            if ($selectedSection !== 'all' && in_array($selectedSection, $teacherSections)) {
                $difficultyQuery->where('student_profile.section', $selectedSection);
            } else {
                // Only show sections that the teacher handles
                $difficultyQuery->whereIn('student_profile.section', $teacherSections);
            }

            $difficultyBreakdown = $difficultyQuery
                ->selectRaw('
                    questions.difficulty_level,
                    COUNT(*) as total,
                    SUM(question_responses.is_correct) as correct,
                    AVG(question_responses.response_time) as avg_time
                ')
                ->groupBy('questions.difficulty_level')
                ->get()
                ->keyBy('difficulty_level');

            return [
                'total_assessments' => $overallStats->total_assessments ?? 0,
                'total_questions' => round($overallStats->avg_questions ?? 0),
                'correct_answers' => round($overallStats->avg_correct ?? 0),
                'accuracy' => round($overallStats->avg_accuracy ?? 0, 1),
                'total_time' => round($overallStats->total_time ?? 0, 2),
                'avg_time_per_question' => round($overallStats->avg_response_time ?? 0, 2),
                'difficulty_breakdown' => $difficultyBreakdown,
                'competency' => $competency,
                'difficulty_level' => $assessmentType
            ];
        } catch (\Exception $e) {
            \Log::error('Error fetching assessment statistics: ' . $e->getMessage());
            return [
                'total_assessments' => 0,
                'total_questions' => 0,
                'correct_answers' => 0,
                'accuracy' => 0,
                'total_time' => 0,
                'avg_time_per_question' => 0,
                'difficulty_breakdown' => collect([]),
                'competency' => $competency,
                'difficulty_level' => $assessmentType
            ];
        }
    }

    /**
     * Get overall assessment statistics (legacy method - keeping for compatibility)
     */
    private function getAssessmentStatistics($assessmentId)
    {
        $assessment = DB::table('assessments')
            ->where('assessment_id', $assessmentId)
            ->first();

        $responses = DB::table('question_responses')
            ->where('assessment_id', $assessmentId)
            ->get();

        $totalQuestions = $responses->count();
        $correctAnswers = $responses->where('is_correct', 1)->count();
        $totalTime = $responses->sum('response_time');
        $avgTime = $totalQuestions > 0 ? $totalTime / $totalQuestions : 0;

        // Group by difficulty level
        $difficultyBreakdown = DB::table('question_responses')
            ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
            ->where('question_responses.assessment_id', $assessmentId)
            ->selectRaw('
                questions.difficulty_level,
                COUNT(*) as total,
                SUM(question_responses.is_correct) as correct,
                AVG(question_responses.response_time) as avg_time
            ')
            ->groupBy('questions.difficulty_level')
            ->get()
            ->keyBy('difficulty_level');

        return [
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'accuracy' => $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : 0,
            'total_time' => round($totalTime, 2),
            'avg_time_per_question' => round($avgTime, 2),
            'difficulty_breakdown' => $difficultyBreakdown,
            'competency' => $assessment->competency ?? 'Unknown',
            'difficulty_level' => $assessment->difficulty_level ?? 'Unknown'
        ];
    }

    /**
     * Sort question details based on the specified criteria
     */
    private function sortQuestionDetails($questionDetails, $sortBy)
    {
        switch ($sortBy) {
            case 'accuracy':
                uasort($questionDetails, function ($a, $b) {
                    return $b['accuracy_rate'] <=> $a['accuracy_rate'];
                });
                break;
            case 'difficulty':
                uasort($questionDetails, function ($a, $b) {
                    $difficultyOrder = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3];
                    $aOrder = $difficultyOrder[$a['question']->difficulty_level] ?? 0;
                    $bOrder = $difficultyOrder[$b['question']->difficulty_level] ?? 0;
                    return $aOrder <=> $bOrder;
                });
                break;
            case 'time':
                uasort($questionDetails, function ($a, $b) {
                    return $a['avg_response_time'] <=> $b['avg_response_time'];
                });
                break;
            case 'bkt_improvement':
                uasort($questionDetails, function ($a, $b) {
                    $aImprovement = $a['bkt_improvement'] ?? 0;
                    $bImprovement = $b['bkt_improvement'] ?? 0;
                    return $bImprovement <=> $aImprovement;
                });
                break;
            default:
                // Default to accuracy sorting
                uasort($questionDetails, function ($a, $b) {
                    return $b['accuracy_rate'] <=> $a['accuracy_rate'];
                });
        }
        
        return $questionDetails;
    }
}
