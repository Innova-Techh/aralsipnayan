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
            return redirect()->route('login');
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

        // Get section comparison statistics
        $sectionStats = $this->getSectionStatistics($teacherSections);

        // Debug: Check if there's any assessment data at all
        $totalAssessments = DB::table('assessments')->count();
        $completedAssessments = DB::table('assessments')->where('status', 'completed')->count();
        $regularAssessments = DB::table('assessments')->where('assessment_type', 'regular')->count();
        

        return view('admin.teacher.analytics.index', [
            'recentAssessments' => $recentAssessments,
            'statistics' => $statistics,
            'teacherSections' => $teacherSections,
            'sectionStats' => $sectionStats
        ]);
    }

    /**
     * Get section-based statistics for comparison charts
     */
    private function getSectionStatistics($teacherSections = [])
    {
        try {
            if (empty($teacherSections)) {
                return [
                    'avg_scores' => [],
                    'avg_accuracy' => [],
                    'avg_time' => []
                ];
            }

            $avgScores = [];
            $avgAccuracy = [];
            $avgTime = [];

            foreach ($teacherSections as $section) {
                // Get average score per section - using the correct column names from the schema
                $scoreData = DB::table('assessments')
                    ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                    ->where('student_profile.section', $section)
                    ->where('assessments.status', 'completed')
                    ->where('assessments.assessment_type', 'regular')
                    ->selectRaw('
                        AVG(COALESCE(assessments.final_mastery_score, assessments.correct_answers)) as avg_score,
                        AVG(assessments.accuracy_percentage) as avg_accuracy,
                        AVG(assessments.total_time_spent) as avg_time_spent,
                        COUNT(*) as total_assessments
                    ')
                    ->first();

                
                // Also log a simple count query to see if we're finding any records
                $countQuery = DB::table('assessments')
                    ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                    ->where('student_profile.section', $section)
                    ->where('assessments.status', 'completed')
                    ->where('assessments.assessment_type', 'regular')
                    ->count();

                // Store data for this section
                $avgScores[] = round($scoreData->avg_score ?? 0, 2);
                $avgAccuracy[] = round($scoreData->avg_accuracy ?? 0, 2);
                
                // Convert seconds to minutes
                $timeInMinutes = ($scoreData->avg_time_spent ?? 0) / 60;
                $avgTime[] = round($timeInMinutes, 2);
            }

            // Get category performance data
            $categoryPerformance = $this->getCategoryPerformance($teacherSections);

            // If no real data found, provide sample data for demonstration
            if (array_sum($avgScores) == 0 && array_sum($avgAccuracy) == 0) {
                $avgScores = array_fill(0, count($teacherSections), 0);
                $avgAccuracy = array_fill(0, count($teacherSections), 0);
                $avgTime = array_fill(0, count($teacherSections), 0);
                $categoryPerformance = [0, 0, 0];
            }

            return [
                'avg_scores' => $avgScores,
                'avg_accuracy' => $avgAccuracy,
                'avg_time' => $avgTime,
                'category_performance' => $categoryPerformance
            ];

        } catch (\Exception $e) {
            return [
                'avg_scores' => array_fill(0, count($teacherSections), 0),
                'avg_accuracy' => array_fill(0, count($teacherSections), 0),
                'avg_time' => array_fill(0, count($teacherSections), 0),
                'category_performance' => [0, 0, 0]
            ];
        }
    }

    /**
     * Get category performance data for charts
     */
    private function getCategoryPerformance($teacherSections = [])
    {
        try {
            if (empty($teacherSections)) {
                return [0, 0, 0];
            }

            $categories = ['number_algebra', 'measurement_geometry', 'data_probability'];
            $categoryPerformance = [];

            foreach ($categories as $category) {
                $performance = DB::table('assessments')
                    ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                    ->whereIn('student_profile.section', $teacherSections)
                    ->where('assessments.competency', $category)
                    ->where('assessments.status', 'completed')
                    ->where('assessments.assessment_type', 'regular')
                    ->selectRaw('
                        AVG(assessments.accuracy_percentage) as avg_performance,
                        COUNT(*) as total_assessments
                    ')
                    ->first();


                $categoryPerformance[] = round($performance->avg_performance ?? 0, 2);
            }

            return $categoryPerformance;

        } catch (\Exception $e) {
            return [0, 0, 0];
        }
    }

    /**
     * Show detailed assessment view
     */
    public function showAssessmentDetails(Request $request, $assessmentId)
    {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return redirect()->route('login');
        }

        // Handle Quiz IDs (format: quiz_{id})
        if (str_starts_with($assessmentId, 'quiz_')) {
            $quizId = substr($assessmentId, 5);
            return $this->showQuizDetails($request, $quizId);
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
     * Show detailed analytics for a specific quiz (Teacher Assessment)
     */
    private function showQuizDetails(Request $request, $quizId)
    {
        $teacherId = Auth::guard('admin')->user()->id;
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacherId)->first();
        $teacherSections = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->pluck('section')
            ->toArray();

        $selectedSection = $request->get('section', 'all');
        $sortBy = $request->get('sort', 'accuracy');

        // Get Quiz Details
        $quiz = DB::table('teacher_assessments')
            ->where('id', $quizId)
            ->first();

        if (!$quiz) {
            return redirect()->route('teacher.analytics')->with('error', 'Quiz not found.');
        }

        // Build base query for sessions
        $sessionsQuery = DB::table('teacher_assessment_sessions')
            ->join('student_profile', 'teacher_assessment_sessions.user_id', '=', 'student_profile.user_id')
            ->where('teacher_assessment_sessions.teacher_assessment_id', $quizId)
            ->where('teacher_assessment_sessions.status', 'completed');

        if ($selectedSection !== 'all' && in_array($selectedSection, $teacherSections)) {
            $sessionsQuery->where('student_profile.section', $selectedSection);
        } else {
             $sessionsQuery->whereIn('student_profile.section', $teacherSections);
        }

        // Get Summary Stats
        $assessmentSummary = (clone $sessionsQuery)
            ->selectRaw('
                COUNT(*) as total_completed,
                MAX(teacher_assessment_sessions.completed_at) as latest_completed_at,
                AVG(teacher_assessment_sessions.questions_answered) as avg_questions,
                AVG(teacher_assessment_sessions.accuracy_percentage) as avg_accuracy
            ')
            ->first();
            
        // Map fields for view compatibility
        $assessmentSummary->competency = $quiz->category;
        $assessmentSummary->difficulty_level = $quiz->difficulty;

        // Get Question Statistics
        // We need to join teacher_quiz_responses linked to these sessions
        // And teacher_assessment_questions for text
        $questionStatsQuery = DB::table('teacher_quiz_responses')
            ->join('teacher_assessment_sessions', 'teacher_quiz_responses.session_id', '=', 'teacher_assessment_sessions.session_id')
            ->join('student_profile', 'teacher_assessment_sessions.user_id', '=', 'student_profile.user_id')
            ->join('teacher_assessment_questions', function($join) {
                // Determine join logic. Responses have pool_id/question_id but might not map directly to teacher_assessment_questions ID if they are from a pool.
                // Actually teacher_assessment_questions has questions from the bank.
                // We typically need to join back to the question bank or use the data stored in teacher_responses.
                // However, teacher_quiz_responses SHOULD store question_id.
                // Let's check getQuestionDetails logic in student controller. It fetches from JSON.
                // But specifically for this view, we need the question text.
                // teacher_assessment_questions links assessment to questions.
                $join->on('teacher_quiz_responses.question_id', '=', 'teacher_assessment_questions.question_id')
                     ->where('teacher_assessment_questions.teacher_assessment_id', '=', DB::raw('teacher_assessment_sessions.teacher_assessment_id'));
            })
             ->where('teacher_assessment_sessions.teacher_assessment_id', $quizId)
             ->where('teacher_assessment_sessions.status', 'completed');

        if ($selectedSection !== 'all' && in_array($selectedSection, $teacherSections)) {
            $questionStatsQuery->where('student_profile.section', $selectedSection);
        } else {
             $questionStatsQuery->whereIn('student_profile.section', $teacherSections);
        }

        $questionStats = $questionStatsQuery
            ->select([
                'teacher_quiz_responses.question_id',
                'teacher_quiz_responses.topic_tag', // Assuming this was added to schema or available
                'teacher_quiz_responses.difficulty_level',
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(teacher_quiz_responses.is_correct) as correct_answers'),
                DB::raw('AVG(teacher_quiz_responses.time_taken) as avg_response_time'),
                DB::raw('COUNT(DISTINCT teacher_quiz_responses.student_id) as unique_students'),
                DB::raw('AVG(teacher_quiz_responses.bkt_probability_before) as avg_bkt_before'),
                DB::raw('AVG(teacher_quiz_responses.bkt_probability_after) as avg_bkt_after'),
                // We need question text. teacher_quiz_responses doesn't have it.
                // We might need to fetch it separately or rely on a helper.
                // For now, let's try to get it from the JSON via helper, loop after query.
            ])
            ->groupBy('teacher_quiz_responses.question_id', 'teacher_quiz_responses.topic_tag', 'teacher_quiz_responses.difficulty_level')
            ->get();
            
        // Post-process question stats to add text and format
        $questionDetails = [];
        $controller = new \App\Http\Controllers\Student\StudentTeacherAssessmentController(); // Re-use helper logic if private methods allow or duplicate
        // Actually, we can reuse the helper logic if we duplicate getQuestionDetails here or move it to a trait/service.
        // For expediency, I'll implement a simple fetcher here similar to StudentTeacherAssessmentController.
        
        foreach ($questionStats as $stat) {
             // Mock the question object structure expected by the view
             $qDetails = $this->getQuestionDetails($stat->question_id); // Need to implement this in this controller
             if (!$qDetails) {
                 $qDetails = [
                     'question_text' => 'Question ID: ' . $stat->question_id,
                     'question_type' => 'unknown',
                     'choice_a' => null, 'choice_b' => null, 'choice_c' => null, 'choice_d' => null,
                     'correct_answer' => null, 'explanation' => null, 'topic_tag' => $stat->topic_tag
                 ];
             }
             
             // Wrap as object for view
             $questionObj = (object) array_merge($qDetails, [
                 'question_id' => $stat->question_id,
                 'difficulty_level' => $stat->difficulty_level, // or from JSON
                 'topic_tag' => $qDetails['topic_tag'] ?? $stat->topic_tag
             ]);

             $questionDetails[$stat->question_id] = [
                'question' => $questionObj,
                'total_attempts' => $stat->total_attempts,
                'correct_answers' => $stat->correct_answers,
                'wrong_answers' => $stat->total_attempts - $stat->correct_answers,
                'accuracy_rate' => $stat->total_attempts > 0 ? round(($stat->correct_answers / $stat->total_attempts) * 100, 1) : 0,
                'avg_response_time' => round($stat->avg_response_time, 2),
                'unique_students' => $stat->unique_students,
                'avg_bkt_before' => round($stat->avg_bkt_before, 4),
                'avg_bkt_after' => round($stat->avg_bkt_after, 4),
                'bkt_improvement' => round($stat->avg_bkt_after - $stat->avg_bkt_before, 4),
                'avg_time_score' => 0, // Not tracking this for quizzes yet explicitly
             ];
        }
        
        // Sort
        $questionDetails = $this->sortQuestionDetails($questionDetails, $sortBy);

        // Assessment Stats (Overall)
        $totalSessions = (clone $sessionsQuery)->count();
        $avgAccuracy = (clone $sessionsQuery)->avg('accuracy_percentage');
        
        // Avg Response Time (Avg of all question responses)
        $avgResponseTime = $questionStats->avg('avg_response_time');
        
        // Difficulty Breakdown
        $difficultyBreakdown = $questionStats->groupBy('difficulty_level')->map(function($group) {
            $total = $group->sum('total_attempts');
            $correct = $group->sum('correct_answers');
            return (object)[
                'total' => $total,
                'correct' => $correct,
                'avg_time' => $group->avg('avg_response_time')
            ];
        });

        $assessmentStats = [
            'total_assessments' => $totalSessions,
            'total_questions' => $assessmentSummary->avg_questions, // Approximate
            'correct_answers' => 0, // Not really needed for overall view summary
            'accuracy' => round($avgAccuracy, 1),
            'total_time' => 0,
            'avg_time_per_question' => round($avgResponseTime, 2),
            'difficulty_breakdown' => $difficultyBreakdown,
            'competency' => $quiz->category,
            'difficulty_level' => $quiz->difficulty
        ];

        return view('admin.teacher.analytics.assessment-details', [
            'assessment' => $assessmentSummary,
            'questionDetails' => $questionDetails,
            'assessmentStats' => $assessmentStats,
            'competency' => $quiz->category,
            'assessmentType' => 'quiz', // To denote it's a quiz type display
            'teacherSections' => $teacherSections,
            'selectedSection' => $selectedSection,
            'sortBy' => $sortBy
        ]);
    }
    
    /**
     * Helper to get question details (Duplicate from Student Controller for independence)
     */
    private function getQuestionDetails($questionId)
    {
        $categoryMap = ['NA' => 'number_algebra', 'MG' => 'measurement_geometry', 'DP' => 'data_probability'];
        $difficultyMap = ['B' => 'beginner', 'I' => 'intermediate', 'A' => 'advanced'];

        if (preg_match('/^(NA|MG|DP)-(B|I|A)-(\d{3})$/', $questionId, $matches)) {
            $categoryPrefix = $categoryMap[$matches[1]] ?? null;
            $difficulty = $difficultyMap[$matches[2]] ?? null;
            
            if ($categoryPrefix && $difficulty) {
                $filePath = base_path("database/data/{$categoryPrefix}/{$categoryPrefix}_{$difficulty}.json");
                if (file_exists($filePath)) {
                    $json = json_decode(file_get_contents($filePath), true);
                    foreach ($json ?? [] as $q) {
                        if ($q['question_id'] === $questionId) {
                            return [
                                'question_text' => $q['question_text'],
                                'question_type' => $q['question_type'],
                                'choice_a' => $q['choice_a'] ?? null,
                                'choice_b' => $q['choice_b'] ?? null,
                                'choice_c' => $q['choice_c'] ?? null,
                                'choice_d' => $q['choice_d'] ?? null,
                                'correct_answer' => $q['correct_answer'],
                                'explanation' => $q['explanation'] ?? null,
                                'topic_tag' => $q['topic_tag'] ?? null
                            ];
                        }
                    }
                }
            }
        }
        return null;
    }

    /**
     * Get recent assessments with real data
     */
    private function getRecentAssessments($teacherSections = [])
    {
        try {
            // 1. Regular Assessments
            $regularAssessments = DB::table('assessments')
                ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                ->select([
                    'assessments.competency',
                    'assessments.difficulty_level',
                    'assessments.assessment_type',
                    DB::raw('MAX(assessments.completed_at) as latest_completed_at'),
                    DB::raw('COUNT(*) as total_completed'),
                    DB::raw('SUM(assessments.questions_answered) as total_questions_answered'),
                    DB::raw('NULL as title') // Regular assessments use competency/difficulty
                ])
                ->where('assessments.status', 'completed')
                ->where('assessments.assessment_type', 'regular')
                ->whereNotNull('assessments.completed_at')
                ->where('assessments.questions_answered', '>', 0)
                ->whereIn('student_profile.section', $teacherSections)
                ->groupBy('assessments.competency', 'assessments.difficulty_level', 'assessments.assessment_type')
                ->get();

            // 2. Quiz Assessments (Teacher Created)
            $quizAssessments = DB::table('teacher_assessment_sessions')
                ->join('teacher_assessments', 'teacher_assessment_sessions.teacher_assessment_id', '=', 'teacher_assessments.id')
                ->join('student_profile', 'teacher_assessment_sessions.user_id', '=', 'student_profile.user_id')
                ->select([
                    'teacher_assessments.id', // Add ID for link generation
                    'teacher_assessments.category as competency',
                    'teacher_assessments.difficulty as difficulty_level',
                    DB::raw('"quiz" as assessment_type'),
                    DB::raw('MAX(teacher_assessment_sessions.completed_at) as latest_completed_at'),
                    DB::raw('COUNT(*) as total_completed'),
                    DB::raw('SUM(teacher_assessment_sessions.questions_answered) as total_questions_answered'),
                    'teacher_assessments.title'
                ])
                ->where('teacher_assessment_sessions.status', 'completed')
                ->whereNotNull('teacher_assessment_sessions.completed_at')
                ->where('teacher_assessment_sessions.questions_answered', '>', 0)
                ->whereIn('student_profile.section', $teacherSections)
                ->groupBy('teacher_assessments.id', 'teacher_assessments.title', 'teacher_assessments.category', 'teacher_assessments.difficulty')
                ->get();

            // Merge collections
            $allAssessments = $regularAssessments->concat($quizAssessments);

            // Sort by latest completed date descending
            $sortedAssessments = $allAssessments->sortByDesc('latest_completed_at')->values();
            
            $mapped = $sortedAssessments->map(function ($assessment) {
                // Format competency name for display
                $competencyNames = [
                    'number_algebra' => 'Number and Algebra',
                    'measurement_geometry' => 'Measurement and Geometry', 
                    'data_probability' => 'Data and Probability',
                    'Number & Algebra' => 'Number and Algebra',
                    'Measurement & Geometry' => 'Measurement and Geometry',
                    'Data & Probability' => 'Data and Probability'
                ];
                
                $competencyDisplay = $competencyNames[$assessment->competency] ?? ucfirst(str_replace('_', ' ', $assessment->competency));
                $assessment->competency_display = $competencyDisplay;
                $assessment->formatted_date = Carbon::parse($assessment->latest_completed_at)->format('M d, Y');
                
                if ($assessment->assessment_type === 'quiz') {
                    $assessment->assessment_name = $assessment->title . ' - quiz';
                    $assessment->assessment_id = 'quiz_' . ($assessment->id ?? uniqid()); // Ensure unique ID for link
                } else {
                     $assessment->assessment_name = $competencyDisplay . ' ' . ucfirst($assessment->difficulty_level ?? 'Unknown');
                     $assessment->assessment_id = $assessment->competency . '_' . ($assessment->difficulty_level ?? 'unknown');
                }
                
                return $assessment;
            });
            
            return $mapped;
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Get overall statistics
     */
    private function getOverallStatistics($teacherSections = [])
    {
        try {
            // 1. Regular Stats
            $regularStats = DB::table('assessments')
                ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                ->where('assessments.status', 'completed')
                ->where('assessments.assessment_type', 'regular')
                ->whereIn('student_profile.section', $teacherSections)
                ->selectRaw('
                    COUNT(*) as total_assessments,
                    AVG(assessments.accuracy_percentage) as avg_accuracy
                ')
                ->first();

            // 2. Quiz Stats
            $quizStats = DB::table('teacher_assessment_sessions')
                ->join('student_profile', 'teacher_assessment_sessions.user_id', '=', 'student_profile.user_id')
                ->where('teacher_assessment_sessions.status', 'completed')
                ->whereIn('student_profile.section', $teacherSections)
                ->selectRaw('
                    COUNT(*) as total_assessments,
                    AVG(teacher_assessment_sessions.accuracy_percentage) as avg_accuracy
                ')
                ->first();

            // Combine
            $totalAssessments = ($regularStats->total_assessments ?? 0) + ($quizStats->total_assessments ?? 0);
            
            // Weighted average for accuracy
            $totalRegularAccuracy = ($regularStats->total_assessments ?? 0) * ($regularStats->avg_accuracy ?? 0);
            $totalQuizAccuracy = ($quizStats->total_assessments ?? 0) * ($quizStats->avg_accuracy ?? 0);
            
            $avgAccuracy = $totalAssessments > 0 ? ($totalRegularAccuracy + $totalQuizAccuracy) / $totalAssessments : 0;

            $totalStudents = DB::table('student_profile')
                ->whereIn('section', $teacherSections)
                ->count();

            // Completion Rate (Simplified: Total completed / (Total Students * Assumed Assignments))
            // For now, let's keep it based on active assignments or just raw completed count vs total possible if trackable.
            // Since we don't have a rigid "assigned total" easily available without querying all assignments:
            // We will stick to the previous completion logic but apply it across both types roughly,
            // or just use the raw count of completed assessments vs some metric.
            
            // Let's stick to the previous simplistic "Completion Rate" logic but extended.
            // Actually the previous logic was: count(completed) / count(all rows in assessments table for that section).
            // For quizzes, rows are only created on start? No, teacher_assessment_sessions are created on start.
            // So for quizzes, "completion rate" of STARTED sessions is:
            
            $quizCompletion = DB::table('teacher_assessment_sessions')
                ->join('student_profile', 'teacher_assessment_sessions.user_id', '=', 'student_profile.user_id')
                ->whereIn('student_profile.section', $teacherSections)
                ->selectRaw('
                     COUNT(CASE WHEN teacher_assessment_sessions.status = "completed" THEN 1 END) as completed,
                     COUNT(*) as total
                ')
                ->first();

             $regularCompletion = DB::table('assessments')
                ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                ->where('assessments.assessment_type', 'regular')
                ->whereIn('student_profile.section', $teacherSections)
                ->selectRaw('
                    COUNT(CASE WHEN assessments.status = "completed" THEN 1 END) as completed,
                    COUNT(*) as total
                ')
                ->first();

            $totalCompleted = ($regularCompletion->completed ?? 0) + ($quizCompletion->completed ?? 0);
            $totalAttempts = ($regularCompletion->total ?? 0) + ($quizCompletion->total ?? 0);

            return [
                'total_assessments' => $totalAssessments,
                'total_students' => $totalStudents,
                'avg_accuracy' => round($avgAccuracy, 1),
                'completion_rate' => $totalAttempts > 0 ? 
                    round(($totalCompleted / $totalAttempts) * 100, 1) : 0
            ];
        } catch (\Exception $e) {
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
