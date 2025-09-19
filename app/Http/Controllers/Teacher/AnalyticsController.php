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

        // Get recent assessments with real data
        $recentAssessments = $this->getRecentAssessments();
        
        // Get overall statistics
        $statistics = $this->getOverallStatistics();

        return view('admin.teacher.analytics.index', [
            'recentAssessments' => $recentAssessments,
            'statistics' => $statistics
        ]);
    }

    /**
     * Show detailed assessment view
     */
    public function showAssessmentDetails($assessmentId)
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

        // Get detailed question statistics for this assessment type
        $questionDetails = $this->getQuestionStatisticsForType($competency, $assessmentType);
        
        // Get overall assessment statistics
        $assessmentStats = $this->getAssessmentStatisticsForType($competency, $assessmentType);

        return view('admin.teacher.analytics.assessment-details', [
            'assessment' => $assessmentSummary,
            'questionDetails' => $questionDetails,
            'assessmentStats' => $assessmentStats,
            'competency' => $competency,
            'assessmentType' => $assessmentType
        ]);
    }

    /**
     * Get recent assessments with real data
     */
    private function getRecentAssessments()
    {
        try {
            // Get unique assessment types (competency + difficulty combinations) with their latest completion date
            // Only include regular assessments, exclude diagnostics
            $results = DB::table('assessments')
                ->select([
                    'competency',
                    'difficulty_level',
                    DB::raw('MAX(completed_at) as latest_completed_at'),
                    DB::raw('COUNT(*) as total_completed'),
                    DB::raw('SUM(questions_answered) as total_questions_answered')
                ])
                ->where('status', 'completed')
                ->where('assessment_type', 'regular') // Only regular assessments
                ->whereNotNull('completed_at')
                ->where('questions_answered', '>', 0)
                ->groupBy('competency', 'difficulty_level')
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
    private function getOverallStatistics()
    {
        try {
            $totalAssessments = DB::table('assessments')
                ->where('status', 'completed')
                ->where('assessment_type', 'regular') // Only regular assessments
                ->count();

            $totalStudents = DB::table('users')
                ->where('role', 'Student')
                ->count();

            $avgAccuracy = DB::table('assessments')
                ->where('status', 'completed')
                ->where('assessment_type', 'regular') // Only regular assessments
                ->whereNotNull('accuracy_percentage')
                ->avg('accuracy_percentage');

            $completionRate = DB::table('assessments')
                ->where('assessment_type', 'regular') // Only regular assessments
                ->selectRaw('
                    COUNT(CASE WHEN status = "completed" THEN 1 END) as completed,
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
    private function getQuestionStatisticsForType($competency, $assessmentType)
    {
        try {
            // Get all questions that were used in this assessment type
            $questionStats = DB::table('question_responses')
                ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
                ->join('assessments', 'question_responses.assessment_id', '=', 'assessments.assessment_id')
                ->where('assessments.competency', $competency)
                ->where('assessments.difficulty_level', $assessmentType)
                ->where('assessments.assessment_type', 'regular') // Only regular assessments
                ->where('assessments.status', 'completed')
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
    private function getAssessmentStatisticsForType($competency, $assessmentType)
    {
        try {
            // Get overall statistics for this assessment type
            $overallStats = DB::table('assessments')
                ->where('competency', $competency)
                ->where('difficulty_level', $assessmentType)
                ->where('assessment_type', 'regular') // Only regular assessments
                ->where('status', 'completed')
                ->selectRaw('
                    COUNT(*) as total_assessments,
                    AVG(questions_answered) as avg_questions,
                    AVG(correct_answers) as avg_correct,
                    AVG(accuracy_percentage) as avg_accuracy,
                    SUM(total_time_spent) as total_time,
                    AVG(average_response_time) as avg_response_time
                ')
                ->first();

            // Get difficulty breakdown
            $difficultyBreakdown = DB::table('question_responses')
                ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
                ->join('assessments', 'question_responses.assessment_id', '=', 'assessments.assessment_id')
                ->where('assessments.competency', $competency)
                ->where('assessments.difficulty_level', $assessmentType)
                ->where('assessments.assessment_type', 'regular') // Only regular assessments
                ->where('assessments.status', 'completed')
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
}
