<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\MasteryProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherReviewStudentController extends Controller
{
    /**
     * Show student profile page
     */
    public function showProfile($student)
    {
        try {
            $studentId = $student;
            
            // Get student information with all related data
            $student = DB::table('users')
                ->join('student_profile', 'users.id', '=', 'student_profile.user_id')
                ->leftJoin('user_progress', 'users.id', '=', 'user_progress.user_id')
                ->where('users.id', $studentId)
                ->where('users.role', 'Student')
                ->select([
                    'users.id',
                    'users.username',
                    'users.email',
                    'student_profile.avatar_url as avatar',
                    'student_profile.firstname',
                    'student_profile.lastname',
                    'student_profile.section',
                    'student_profile.grade_level',
                    'student_profile.student_id',
                    'student_profile.current_streak',
                    'student_profile.longest_streak',
                    // Get total_points from user_progress, fallback to student_profile
                    DB::raw('COALESCE(user_progress.total_points, student_profile.total_points, 0) as total_points'),
                    'user_progress.current_level',
                    'user_progress.current_rank'
                ])
                ->first();
            
            if (!$student) {
                Log::warning('Student not found', ['student_id' => $studentId]);
                abort(404, 'Student not found');
            }
            
            
            // Add full name
            $student->name = $student->firstname . ' ' . $student->lastname;
            
            // Get section information - make it optional
            $section = DB::table('sections')
                ->where('name', $student->section)
                ->first();
            
            // If section not found, create a dummy object
            if (!$section) {
                $section = (object) [
                    'name' => $student->section,
                    'id' => null
                ];
            }
            
            // 🟩 Get student's status
            $status = DB::table('users')
            ->where('id', $studentId)
            ->value('status');

            if ($status === 'active') {
            // 🟩 Compute student's rank within their section
            $studentPoints = DB::table('user_progress')
                ->where('user_id', $studentId)
                ->value('total_points') ?? 0;

            $higherCount = DB::table('student_profile')
                ->join('users', 'student_profile.user_id', '=', 'users.id')
                ->leftJoin('user_progress', 'student_profile.user_id', '=', 'user_progress.user_id')
                ->where('student_profile.section', $student->section)
                ->where('student_profile.grade_level', $student->grade_level)
                ->where('users.status', 'active')
                ->whereRaw('COALESCE(user_progress.total_points, 0) > ?', [$studentPoints])
                ->count();

            $student->section_rank = $higherCount + 1;
            } else {
            // 🟥 Don't assign rank to inactive users
            $student->section_rank = null; // or 'N/A'
            }
           
            // Get mastery data for all competencies
            $masteryData = DB::table('student_mastery')
                ->where('user_id', $studentId)
                ->get();
            
            
            // Calculate competency scores with defaults
            $competencyScores = [
                'numerical_literacy_score' => 0,
                'algebraic_thinking_score' => 0,
                'geometric_reasoning_score' => 0,
                'problem_solving_score' => 0
            ];
            
            foreach ($masteryData as $mastery) {
                $score = $mastery->final_mastery_score ?? 0;
                
                switch ($mastery->competency) {
                    case 'number_algebra':
                        $competencyScores['numerical_literacy_score'] = round($score);
                        $competencyScores['algebraic_thinking_score'] = round($score);
                        break;
                    case 'measurement_geometry':
                        $competencyScores['geometric_reasoning_score'] = round($score);
                        break;
                    case 'data_probability':
                        $competencyScores['problem_solving_score'] = round($score);
                        break;
                }
            }
            
            // Add competency scores to student object (for display compatibility)
            $student->numerical_literacy_score = $competencyScores['numerical_literacy_score'] ?? 0;
            $student->algebraic_thinking_score = $competencyScores['algebraic_thinking_score'] ?? 0;
            $student->geometric_reasoning_score = $competencyScores['geometric_reasoning_score'] ?? 0;
            $student->problem_solving_score = $competencyScores['problem_solving_score'] ?? 0;
            
            // Get detailed competency metrics (accuracy, BKT score, avg response time)
            $student->competency_details = $this->getCompetencyDetails($studentId);
            
            // Get assessment statistics first
            $assessmentStats = $this->getAssessmentStatistics($studentId);
            $student->assessment_count = $assessmentStats['total_count'];
            $student->regular_count = $assessmentStats['regular_count'];
            $student->diagnostic_count = $assessmentStats['diagnostic_count'];
            $student->best_streak = $student->longest_streak ?? 0;
            
            // Get diagnostics completion status
            $student->diagnostics_completed = $this->getDiagnosticsCompletionStatus($studentId);
            
            // Calculate overall score and time metrics ONLY from regular assessments (not diagnostics)
            $regularAssessmentIds = $this->getRegularAssessmentIds($studentId);
            
            
            if (!empty($regularAssessmentIds)) {
                // Calculate overall score from regular assessments only
                $regularMasteryData = DB::table('student_mastery')
                    ->where('user_id', $studentId)
                    ->whereIn('competency', ['number_algebra', 'measurement_geometry', 'data_probability'])
                    ->get();
                
                
                $regularCompetencyScores = [
                    'number_algebra_score' => 0,
                    'measurement_geometry_score' => 0,
                    'data_probability_score' => 0
                ];
                
                foreach ($regularMasteryData as $mastery) {
                    $score = $mastery->final_mastery_score ?? 0;

                    switch ($mastery->competency) {
                        case 'number_algebra':
                            $regularCompetencyScores['number_algebra_score'] = round($score);
                            break;
                        case 'measurement_geometry':
                            $regularCompetencyScores['measurement_geometry_score'] = round($score);
                            break;
                        case 'data_probability':
                            $regularCompetencyScores['data_probability_score'] = round($score);
                            break;
                    }
                }
                
                // Calculate overall score from regular assessments only - exclude zero scores
                $nonZeroScores = array_filter($regularCompetencyScores, function($score) {
                    return $score > 0;
                });
                
                $totalScore = array_sum($nonZeroScores);
                $avgScore = count($nonZeroScores) > 0 ? $totalScore / count($nonZeroScores) : 0;
                $student->overall_score = round($avgScore, 1);
                
                // Calculate time spent and average time per question from regular assessments only
                $regularTimeSeconds = DB::table('question_responses')
                    ->where('user_id', $studentId)
                    ->whereIn('assessment_id', $regularAssessmentIds)
                    ->sum('response_time');
                
                $regularQuestions = DB::table('question_responses')
                    ->where('user_id', $studentId)
                    ->whereIn('assessment_id', $regularAssessmentIds)
                    ->count();
                
                $student->time_spent = round($regularTimeSeconds / 3600, 1);
                $student->avg_time_per_question = $regularQuestions > 0 
                    ? round(($regularTimeSeconds / $regularQuestions) / 60, 1) 
                    : 0;
            } else {
                
                $student->overall_score = 0;
                $student->time_spent = 0;
                $student->avg_time_per_question = 0;
            }
            
            // Get last assessment info
            $lastAssessment = $this->getLastAssessment($studentId);
            
            
            if ($lastAssessment) {
                $student->last_assessment_name = $lastAssessment['name'];
                $student->last_assessment_score = $lastAssessment['score'];
                $student->last_assessment_date = $lastAssessment['date'];
                $student->last_assessment_time = $lastAssessment['time'];
                $student->last_assessment_accuracy = $lastAssessment['accuracy'];
            } else {
                $student->last_assessment_name = 'No assessments yet';
                $student->last_assessment_score = 0;
                $student->last_assessment_date = null;
                $student->last_assessment_time = 'N/A';
                $student->last_assessment_accuracy = '0%';
            }
            
            // Get all assessments for the student
            $student->assessments = $this->getStudentAssessments($studentId);
             
            // Get struggling areas
            $student->struggling_areas = $this->getStrugglingAreas($studentId);
            
            // Get achievements
            $student->achievements = $this->getAchievements($studentId);
            
            // Get mastery progress data
            $masteryProgressService = new MasteryProgressService();
            $masteryProgress = $masteryProgressService->getWeeklyMasteryProgress($studentId);
            
            return view('admin.teacher.sections.student-profile', [
                'student' => $student,
                'section' => $section,
                'masteryProgress' => $masteryProgress
            ]);
            
        } catch (\Exception $e) {
            
            // Show detailed error for debugging
            if (config('app.debug')) {
                dd([
                    'error' => 'Error in showProfile',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'student_id' => $student ?? 'unknown'
                ]);
            }
            
            // Return error view
            return back()->with('error', 'Error loading student profile: ' . $e->getMessage());
        }
    }
    
    /**
     * Get diagnostics completion status for each competency
     */
    private function getDiagnosticsCompletionStatus($studentId)
    {
        $competencies = ['number_algebra', 'measurement_geometry', 'data_probability'];
        $completionStatus = [];
        
        foreach ($competencies as $competency) {
            $completed = DB::table('diagnostic_sessions')
                ->where('user_id', $studentId)
                ->where('competency', $competency)
                ->where('status', 'completed')
                ->exists();
            
            $displayName = ucfirst(str_replace('_', ' & ', $competency));
            $completionStatus[$displayName] = $completed;
        }
        
        return $completionStatus;
    }
    
    /**
     * Get all regular assessment IDs (excluding diagnostics)
     */
    private function getRegularAssessmentIds($studentId)
    {
        // Get all unique assessment IDs from question_responses that have responses
        $allAssessmentIds = DB::table('question_responses')
            ->where('user_id', $studentId)
            ->distinct()
            ->pluck('assessment_id')
            ->toArray();
        
        $regularIds = [];
        
        foreach ($allAssessmentIds as $assessmentId) {
            // Check if it's a diagnostic session first
            $isDiagnostic = DB::table('diagnostic_sessions')
                ->where('session_id', $assessmentId)
                ->where('user_id', $studentId)
                ->where('status', 'completed')
                ->exists();
            
            if (!$isDiagnostic) {
                // Check if it's a regular assessment (either from assessments table or assessment_sessions table)
                $isRegular = DB::table('assessments')
                    ->where('assessment_id', $assessmentId)
                    ->where('user_id', $studentId)
                    ->where('status', 'completed')
                    ->where('assessment_type', 'regular')
                    ->exists();
                
                if (!$isRegular) {
                    // Check assessment_sessions table
                    $isRegular = DB::table('assessment_sessions')
                        ->where('session_id', $assessmentId)
                        ->where('user_id', $studentId)
                        ->where('status', 'completed')
                        ->exists();
                }
                
                if ($isRegular) {
                    $regularIds[] = $assessmentId;
                }
            }
        }
        
        
        return $regularIds;
    }
    
    /**
     * Get student's rank within their section
     */
    private function getStudentRankInSection($studentId, $sectionName)
    {
        try {
            $rankings = DB::table('users')
                ->join('student_profile', 'users.id', '=', 'student_profile.user_id')
                ->where('student_profile.section', $sectionName)
                ->where('users.role', 'Student')
                ->orderByDesc('student_profile.total_points')
                ->pluck('users.id')
                ->toArray();
            
            $rank = array_search($studentId, $rankings);
            
            return $rank !== false ? $rank + 1 : null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Get assessment statistics for a student
     */
    private function getAssessmentStatistics($studentId)
    {
        try {
            // Get all unique assessment IDs from question_responses to avoid double counting
            $allAssessmentIds = DB::table('question_responses')
                ->where('user_id', $studentId)
                ->distinct()
                ->pluck('assessment_id')
                ->toArray();
            
            // Count regular assessments (from assessments table and assessment_sessions table)
            $regularCount = 0;
            $diagnosticCount = 0;
            
            foreach ($allAssessmentIds as $assessmentId) {
                // Check if it's a diagnostic session
                $isDiagnostic = DB::table('diagnostic_sessions')
                    ->where('session_id', $assessmentId)
                    ->where('user_id', $studentId)
                    ->where('status', 'completed')
                    ->exists();
                
                if ($isDiagnostic) {
                    $diagnosticCount++;
                } else {
                    // Check if it's a regular assessment (either from assessments table or assessment_sessions table)
                    $isRegular = DB::table('assessments')
                        ->where('assessment_id', $assessmentId)
                        ->where('user_id', $studentId)
                        ->where('status', 'completed')
                        ->where('assessment_type', 'regular')
                        ->exists();
                    
                    if (!$isRegular) {
                        // Check assessment_sessions table
                        $isRegular = DB::table('assessment_sessions')
                            ->where('session_id', $assessmentId)
                            ->where('user_id', $studentId)
                            ->where('status', 'completed')
                            ->exists();
                    }
                    
                    if ($isRegular) {
                        $regularCount++;
                    }
                }
            }
            
            $totalCount = $regularCount + $diagnosticCount;
            
            return [
                'total_count' => $totalCount,
                'regular_count' => $regularCount,
                'diagnostic_count' => $diagnosticCount
            ];
        } catch (\Exception $e) {
            return [
                'total_count' => 0,
                'regular_count' => 0,
                'diagnostic_count' => 0
            ];
        }
    }
    
    /**
     * Get the last assessment taken by student (regular assessments only, not diagnostics)
     */
    private function getLastAssessment($studentId)
    {
        try {
            // Get regular assessment IDs first
            $regularAssessmentIds = $this->getRegularAssessmentIds($studentId);
            
            if (empty($regularAssessmentIds)) {
                return null;
            }
            
            // Get the most recent regular assessment based on the latest question response timestamp
            $mostRecentResponse = DB::table('question_responses')
                ->where('user_id', $studentId)
                ->whereIn('assessment_id', $regularAssessmentIds)
                ->orderByDesc('answered_at')
                ->first();
            
            if (!$mostRecentResponse) {
                return null;
            }
            
            $assessmentId = $mostRecentResponse->assessment_id;
            
            // Get assessment details from the appropriate table
            $assessmentData = null;
            
            // Check if it's from assessments table
            $assessmentFromTable = DB::table('assessments')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $studentId)
                ->where('status', 'completed')
                ->where('assessment_type', 'regular')
                ->first();
            
            if ($assessmentFromTable) {
                $assessmentData = $assessmentFromTable;
            } else {
                // Check assessment_sessions table
                $assessmentSession = DB::table('assessment_sessions')
                    ->where('session_id', $assessmentId)
                    ->where('user_id', $studentId)
                    ->where('status', 'completed')
                    ->first();
                
                if ($assessmentSession) {
                    $assessmentData = $assessmentSession;
                }
            }
            
            if (!$assessmentData) {
                return null;
            }
            
            // Get all question responses for this assessment
            $responses = DB::table('question_responses')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $studentId)
                ->get();
            
            $totalQuestions = $responses->count();
            $correctAnswers = $responses->where('is_correct', 1)->count();
            $totalTime = $responses->sum('response_time');
            
            $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
            $accuracy = $score;
            
            // Format time
            $minutes = floor($totalTime / 60);
            $seconds = $totalTime % 60;
            $timeFormatted = $minutes . '.' . round($seconds / 60 * 10) . ' min';
            
            // Get competency name
            $competencyName = ucfirst(str_replace('_', ' & ', $assessmentData->competency ?? 'Assessment'));
            
            // Use the most recent response timestamp as the completion date
            $completedAt = $mostRecentResponse->answered_at;
            
            return [
                'name' => $competencyName . ' Assessment',
                'score' => $score,
                'date' => date('Y-m-d', strtotime($completedAt)),
                'time' => $timeFormatted,
                'accuracy' => $accuracy . '%'
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Get all assessments for a student
     */
    private function getStudentAssessments($studentId)
    {
        try {
            // Get from assessments table (older system) - ONLY regular assessments
            $assessmentsTable = DB::table('assessments')
                ->where('user_id', $studentId)
                ->where('status', 'completed')
                ->where('assessment_type', 'regular') // Only get regular assessments, not diagnostics
                ->select([
                    'assessment_id as id',
                    'competency',
                    'completed_at as date',
                    'assessment_type as type',
                    'completed_at as time'
                ])
                ->get();
            
            // Get regular assessments from assessment_sessions table (newer system)
            $regularAssessments = DB::table('assessment_sessions')
                ->where('user_id', $studentId)
                ->where('status', 'completed')
                ->select([
                    'session_id as id',
                    'competency',
                    'completed_at as date',
                    'session_type as type',
                    'completed_at as time'
                ])
                ->get();
            
            // Get diagnostic sessions - this is the PRIMARY source for diagnostics
            $diagnosticSessions = DB::table('diagnostic_sessions')
                ->where('user_id', $studentId)
                ->where('status', 'completed')
                ->select([
                    'session_id as id',
                    'competency',
                    'completed_at as date',
                    DB::raw("'diagnostic' as type"),
                    'completed_at as time'
                ])
                ->get();
            
            // Combine all three sources
            $allAssessments = $assessmentsTable
                ->concat($regularAssessments)
                ->concat($diagnosticSessions)
                ->unique('id') // Remove any duplicates based on ID
                ->map(function($assessment) use ($studentId) {
                    // Calculate score from question_responses
                    $responses = DB::table('question_responses')
                        ->where('assessment_id', $assessment->id)
                        ->get();
                    
                    $total = $responses->count();
                    $correct = $responses->where('is_correct', 1)->count();
                    $totalTime = $responses->sum('response_time');
                    
                    // Skip assessments with no responses
                    if ($total === 0) {
                        return null;
                    }
                    
                    $assessment->score = round(($correct / $total) * 100) . '%';
                    
                    // Store original timestamp for sorting
                    $originalTimestamp = $assessment->time;
                    
                    // Format completion time from completed_at field
                    if ($assessment->time) {
                        $assessment->time = date('H:i', strtotime($assessment->time));
                    } else {
                        $assessment->time = 'N/A';
                    }
                    
                    // Format date
                    if ($assessment->date) {
                        $assessment->date = date('Y-m-d', strtotime($assessment->date));
                    }
                    
                    // Store original timestamp for sorting
                    $assessment->sort_timestamp = $originalTimestamp;
                    
                    // Format name based on type
                    $typeStr = strtolower($assessment->type);
                    if ($typeStr === 'diagnostic') {
                        $assessment->name = ucfirst(str_replace('_', ' & ', $assessment->competency)) . ' Diagnostic';
                        $assessment->type = 'Diagnostic';
                    } else {
                        $assessment->name = ucfirst(str_replace('_', ' & ', $assessment->competency));
                        // Capitalize the type
                        $assessment->type = ucfirst($typeStr);
                    }
                    
                    return $assessment;
                })
                ->filter() // Remove null values (assessments with no responses)
                ->sortByDesc('sort_timestamp')
                ->values();
            
            return $allAssessments;
            
        } catch (\Exception $e) {
            return collect([]);
        }
    }
    
    /**
     * Get struggling areas for a student
     */
    private function getStrugglingAreas($studentId)
    {
        try {
            // Get all question responses grouped by topic
            $topicPerformance = DB::table('question_responses')
                ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
                ->where('question_responses.user_id', $studentId)
                ->select([
                    'questions.topic_tag as topic',
                    'questions.competency as area',
                    DB::raw('COUNT(*) as attempts'),
                    DB::raw('SUM(CASE WHEN question_responses.is_correct = 1 THEN 1 ELSE 0 END) as correct'),
                    DB::raw('COUNT(*) as total')
                ])
                ->groupBy('questions.topic_tag', 'questions.competency')
                ->get();
            
            // Filter for topics with less than 70% accuracy
            $strugglingAreas = $topicPerformance->filter(function($topic) {
                $percentage = $topic->total > 0 ? ($topic->correct / $topic->total) * 100 : 0;
                return $percentage < 70 && $topic->attempts >= 3;
            })->map(function($topic) {
                $percentage = $topic->total > 0 ? ($topic->correct / $topic->total) * 100 : 0;
                
                return [
                    'topic' => ucfirst(str_replace('_', ' ', $topic->topic ?? 'General')),
                    'area' => ucfirst(str_replace('_', ' & ', $topic->area)),
                    'score' => round($percentage) . '%',
                    'change' => '-' . round(100 - $percentage) . '%',
                    'attempts' => $topic->attempts . ' attempts'
                ];
            })->take(6)->values()->toArray();
            
            return $strugglingAreas;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Get detailed competency metrics (accuracy, BKT score, avg response time)
     */
    private function getCompetencyDetails($studentId)
    {
        try {
            $competencies = ['number_algebra', 'measurement_geometry', 'data_probability'];
            $competencyDetails = [];
            
            foreach ($competencies as $competency) {
                // Get all question responses for this competency
                $responses = DB::table('question_responses')
                    ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
                    ->where('question_responses.user_id', $studentId)
                    ->where('questions.competency', $competency)
                    ->select([
                        'question_responses.is_correct',
                        'question_responses.response_time',
                        'question_responses.answered_at'
                    ])
                    ->get();
                
                // Calculate accuracy
                $totalQuestions = $responses->count();
                $correctAnswers = $responses->where('is_correct', 1)->count();
                $accuracy = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : 0;
                
                // Calculate average response time (in minutes)
                $totalTime = $responses->sum('response_time');
                $avgResponseTime = $totalQuestions > 0 ? round(($totalTime / $totalQuestions) / 60, 1) : 0;
                
                // Get BKT score from student_mastery table
                $masteryData = DB::table('student_mastery')
                    ->where('user_id', $studentId)
                    ->where('competency', $competency)
                    ->first();
                
                // Use the dedicated bkt_score field (0.0 to 1.0 scale) and convert to percentage
                $bktScore = $masteryData ? round(($masteryData->bkt_score ?? 0) * 100, 1) : 0;
                
                // Map competency to display name
                $displayName = '';
                switch ($competency) {
                    case 'number_algebra':
                        $displayName = 'Number & Algebra';
                        break;
                    case 'measurement_geometry':
                        $displayName = 'Measurement & Geometry';
                        break;
                    case 'data_probability':
                        $displayName = 'Data & Probability';
                        break;
                }
                
                $competencyDetails[$competency] = [
                    'name' => $displayName,
                    'accuracy' => $accuracy,
                    'bkt_score' => $bktScore,
                    'avg_response_time' => $avgResponseTime,
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $correctAnswers,
                    'current_difficulty' => $masteryData ? $masteryData->current_difficulty : 'beginner'
                ];
            }
            
            return $competencyDetails;
            
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Get achievements for a student from user_badges table
     */
    private function getAchievements($studentId)
    {
        try {
            // Get earned badges from user_badges table
            $earnedBadges = DB::table('user_badges')
                ->where('user_id', $studentId)
                ->orderByDesc('awarded_at')
                ->get();
            
            $achievements = [];
            
            foreach ($earnedBadges as $badge) {
                $achievements[] = [
                    'name' => $badge->badge_name,
                    'desc' => $badge->badge_description,
                    'date' => 'Earned ' . date('M d, Y', strtotime($badge->awarded_at))
                ];
            }
            
            // If no badges earned, return empty array
            return $achievements;
            
        } catch (\Exception $e) {
            return [];
        }
    }
}