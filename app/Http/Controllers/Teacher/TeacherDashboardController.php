<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeacherDashboardController extends Controller
{
    /**
     * Show teacher dashboard with real data
     */
    public function index()
    {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return redirect()->route('login');
        }

        $teacherId = Auth::guard('admin')->user()->id;
        
        // Get teacher profile
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacherId)->first();
        \Log::info('DEBUG teacherProfile:', [
            'value' => $teacherProfile,
            'type' => gettype($teacherProfile)
        ]);
        if (!$teacherProfile) {
            return redirect()->route('login')->withErrors(['error' => 'Teacher profile not found.']);
        }

        // Get teacher's sections - try different approaches
        $teacherSections = [];
        
        // First try: teacher_sections table
        try {
            $teacherSections = DB::table('sections')
            ->join('teacher_sections', 'sections.name', '=', 'teacher_sections.section')
            ->where('teacher_sections.teacher_id', $teacherProfile->id)
            ->where('sections.is_active', true)
            ->select('sections.name')
            ->pluck('sections.name'); 
        } catch (\Exception $e) {
            \Log::error('Error accessing teacher_sections table: ' . $e->getMessage());
        }
        


        // Get dashboard data with error handling
        try {
            $dashboardData = $this->getDashboardData($teacherSections);
        } catch (\Exception $e) {
            \Log::error('Error getting dashboard data: ' . $e->getMessage());
            $dashboardData = $this->getDefaultDashboardData();
        }

        // Debug: Check if we have data
        if (empty($teacherSections)) {
            // If no sections, provide default data
            $dashboardData = $this->getDefaultDashboardData();
        }

        // Debug: Log the dashboard data
        \Log::info('Dashboard data keys:', array_keys($dashboardData));
        \Log::info('Dashboard data sample:', $dashboardData);

        // Ensure all required variables are present
        $dashboardData = array_merge([
            'totalStudents' => ['count' => 0, 'growth' => 0, 'growthText' => '0%'],
            'activeAssessments' => ['count' => 0, 'growth' => 0, 'growthText' => '0'],
            'averageScore' => ['score' => 0, 'growth' => 0, 'growthText' => '0%'],
            'responseTime' => ['time' => 0, 'improvement' => 0, 'improvementText' => '0 min'],
            'sectionPerformance' => [],
            'topPerformers' => [],
            'topAccuracyPerformers' => [],
            'recentActivity' => [],
            'topPerformingSection' => null,
            'worstPerformingSection' => null,
            'overallAccuracy' => 0,
        ], $dashboardData);
            
        return view('admin.teacher.index', $dashboardData);
    }

    /**
     * Get all dashboard data
     */
    private function getDashboardData($teacherSections)
    {
        try {
            return [
                'totalStudents' => $this->getTotalStudents($teacherSections),
                'activeAssessments' => $this->getActiveAssessments($teacherSections),
                'averageScore' => $this->getAverageScore($teacherSections),
                'responseTime' => $this->getResponseTime($teacherSections),
                'sectionPerformance' => $this->getSectionPerformance($teacherSections),
                'topPerformers' => $this->getTopPerformers($teacherSections),
                'topAccuracyPerformers' => $this->getTopAccuracyPerformers($teacherSections),
                'recentActivity' => $this->getRecentActivity($teacherSections),
                'topPerformingSection' => $this->getTopPerformingSection($teacherSections),
                'worstPerformingSection' => $this->getWorstPerformingSection($teacherSections),
                'overallAccuracy' => $this->getOverallAccuracy($teacherSections),
            ];
        } catch (\Exception $e) {
            \Log::error('Error in getDashboardData: ' . $e->getMessage());
            return $this->getDefaultDashboardData();
        }
    }

    /**
     * Get total students handled by teacher
     */
    private function getTotalStudents($teacherSections)
    {
        try {
            $totalStudents = DB::table('student_profile')
                ->whereIn('section', $teacherSections)
                ->count();

            // Get previous month count for comparison
            $previousMonth = Carbon::now()->subMonth();
            $previousMonthStudents = DB::table('student_profile')
                ->whereIn('section', $teacherSections)
                ->where('created_at', '<=', $previousMonth)
                ->count();

            $growth = $previousMonthStudents > 0 
                ? round((($totalStudents - $previousMonthStudents) / $previousMonthStudents) * 100, 1)
                : 0;

            return [
                'count' => $totalStudents,
                'growth' => $growth,
                'growthText' => $growth >= 0 ? "+{$growth}%" : "{$growth}%"
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting total students: ' . $e->getMessage());
            return [
                'count' => 0,
                'growth' => 0,
                'growthText' => '0%'
            ];
        }
    }

    /**
     * Get active assessments count
     */
    private function getActiveAssessments($teacherSections)
    {
        $activeAssessments = DB::table('teacher_assessments')
            ->where('created_by', Auth::guard('admin')->user()->id)
            ->where('status', 'active')
            ->count();

        // Get previous month count
        $previousMonth = Carbon::now()->subMonth();
        $previousMonthAssessments = DB::table('teacher_assessments')
            ->where('created_by', Auth::guard('admin')->user()->id)
            ->where('status', 'active')
            ->where('created_at', '<=', $previousMonth)
            ->count();

        $growth = $previousMonthAssessments > 0 
            ? $activeAssessments - $previousMonthAssessments
            : 0;

        return [
            'count' => $activeAssessments,
            'growth' => $growth,
            'growthText' => $growth >= 0 ? "+{$growth}" : "{$growth}"
        ];
    }

    /**
     * Get average score across all sections
     * FIXED: Now calculates average from each section's average performance
     */
    private function getAverageScore($teacherSections)
    {
        try {
            $sectionAverages = [];
            
            foreach ($teacherSections as $section) {
                $sectionAvg = $this->getSectionAveragePerformance($section);
                if ($sectionAvg > 0) {
                    $sectionAverages[] = $sectionAvg;
                }
            }
            
            $averageScore = count($sectionAverages) > 0 
                ? round(array_sum($sectionAverages) / count($sectionAverages), 1)
                : 0;

            // Get previous month average using same method
            $previousMonth = Carbon::now()->subMonth();
            $previousSectionAverages = [];
            
            foreach ($teacherSections as $section) {
                $sectionAvg = $this->getSectionAveragePerformance($section, $previousMonth);
                if ($sectionAvg > 0) {
                    $previousSectionAverages[] = $sectionAvg;
                }
            }
            
            $previousMonthAverage = count($previousSectionAverages) > 0 
                ? round(array_sum($previousSectionAverages) / count($previousSectionAverages), 1)
                : 0;
                
            $growth = $previousMonthAverage > 0 
                ? round($averageScore - $previousMonthAverage, 1)
                : 0;

            return [
                'score' => $averageScore,
                'growth' => $growth,
                'growthText' => $growth >= 0 ? "+{$growth}%" : "{$growth}%"
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting average score: ' . $e->getMessage());
            return [
                'score' => 0,
                'growth' => 0,
                'growthText' => '0%'
            ];
        }
    }

    /**
     * Calculate average performance for a specific section
     * Considers both quiz_results and assessment_sessions
     */
    private function getSectionAveragePerformance($section, $beforeDate = null)
    {
        try {
            $scores = [];
            
            // Get quiz_results scores (teacher-created assessments)
            $quizQuery = DB::table('quiz_results')
                ->join('student_profile', 'quiz_results.student_id', '=', 'student_profile.user_id')
                ->where('student_profile.section', $section);
            
            if ($beforeDate) {
                $quizQuery->where('quiz_results.completed_at', '<=', $beforeDate);
            }
            
            $quizScores = $quizQuery->pluck('quiz_results.percentage')->toArray();
            $scores = array_merge($scores, $quizScores);
            
            // Get assessment_sessions scores (system assessments)
            $sessionQuery = DB::table('assessment_sessions')
                ->join('student_profile', 'assessment_sessions.user_id', '=', 'student_profile.user_id')
                ->where('student_profile.section', $section)
                ->where('assessment_sessions.status', 'completed')
                ->whereNotNull('assessment_sessions.accuracy_percentage');
            
            if ($beforeDate) {
                $sessionQuery->where('assessment_sessions.completed_at', '<=', $beforeDate);
            }
            
            $sessionScores = $sessionQuery->pluck('assessment_sessions.accuracy_percentage')->toArray();
            $scores = array_merge($scores, $sessionScores);
            
            // Calculate average
            return count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : 0;
            
        } catch (\Exception $e) {
            \Log::error("Error calculating section average for {$section}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get average response time
     */
    private function getResponseTime($teacherSections)
    {
        $averageTime = DB::table('quiz_results')
            ->join('student_profile', 'quiz_results.student_id', '=', 'student_profile.user_id')
            ->whereIn('student_profile.section', $teacherSections)
            ->avg('quiz_results.time_taken');

        $averageTime = $averageTime ? round($averageTime / 60, 1) : 0; // Convert to minutes

        // Get previous month average
        $previousMonth = Carbon::now()->subMonth();
        $previousMonthTime = DB::table('quiz_results')
            ->join('student_profile', 'quiz_results.student_id', '=', 'student_profile.user_id')
            ->whereIn('student_profile.section', $teacherSections)
            ->where('quiz_results.completed_at', '<=', $previousMonth)
            ->avg('quiz_results.time_taken');

        $previousMonthTime = $previousMonthTime ? round($previousMonthTime / 60, 1) : 0;
        $improvement = $previousMonthTime > 0 
            ? round($previousMonthTime - $averageTime, 1)
            : 0;

        return [
            'time' => $averageTime,
            'improvement' => $improvement,
            'improvementText' => $improvement >= 0 ? "-{$improvement} min" : "+{$improvement} min"
        ];
    }

    /**
     * Get section performance data
     * FIXED: Now uses getSectionAveragePerformance for accurate calculations
     */
    private function getSectionPerformance($teacherSections)
    {
        $sectionStats = [];

        foreach ($teacherSections as $section) {
            $averageScore = $this->getSectionAveragePerformance($section);

            $sectionStats[] = [
                'section' => $section,
                'averageScore' => $averageScore,
                'color' => $this->getSectionColor($averageScore)
            ];
        }

        // Sort by average score (descending)
        usort($sectionStats, function($a, $b) {
            return $b['averageScore'] <=> $a['averageScore'];
        });

        return $sectionStats;
    }

    /**
     * Get top performers based on total_points
     * FIXED: Now uses total_points from student_profile
     */
    private function getTopPerformers($teacherSections)
    {
        try {
            $topPerformers = DB::table('student_profile')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->join('user_progress', 'student_profile.user_id', '=', 'user_progress.user_id')
            ->whereIn('student_profile.section', $teacherSections)
            ->select(
                'users.id',
                'student_profile.firstname',
                'student_profile.lastname',
                'student_profile.section',
                'user_progress.total_points',
                'student_profile.avatar_url' // optional
            )
            ->orderBy('user_progress.total_points', 'desc')
            ->limit(4)
            ->get();

            return $topPerformers->map(function($student, $index) {
                // Calculate improvement based on recent performance
                $improvement = $this->calculateStudentImprovement($student->id);
                
                return [
                    'rank' => $index + 1,
                    'name' => $student->firstname . ' ' . $student->lastname,
                    'section' => $student->section,
                    'score' => $student->total_points,
                    'avatar' => $student->avatar_url ?: 'images/profile/avatar' . ($index + 1) . '.png',
                    'improvement' => $improvement
                ];
            });
        } catch (\Exception $e) {
            \Log::error('Error getting top performers: ' . $e->getMessage());
            return collect([]);
        }
    }

        /**
     * 🎯 Get top performers by accuracy (from student_mastery)
     */
    private function getTopAccuracyPerformers($teacherSections)
    {
        try {
            $accuracyPerformers = DB::table('student_profile')
                ->join('users', 'student_profile.user_id', '=', 'users.id')
                ->join('student_mastery', 'student_profile.user_id', '=', 'student_mastery.user_id')
                ->whereIn('student_profile.section', $teacherSections)
                ->where('student_mastery.total_questions_answered', '>', 0)
                ->select(
                    'student_profile.user_id as id',
                    'student_profile.firstname',
                    'student_profile.lastname',
                    'student_profile.section',
                    DB::raw('ROUND(SUM(student_mastery.correct_answers) / SUM(student_mastery.total_questions_answered) * 100, 2) AS accuracy')
                )
                ->groupBy('student_profile.user_id', 'student_profile.firstname', 'student_profile.lastname', 'student_profile.section')
                ->orderByDesc('accuracy')
                ->limit(4)
                ->get();

            return $accuracyPerformers->map(function ($student, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => "{$student->firstname} {$student->lastname}",
                    'section' => $student->section,
                    'score' => $student->accuracy,
                    'avatar' => $student->avatar_url ?? "images/profile/avatar" . ($index + 1) . ".png",
                ];
            });
        } catch (\Exception $e) {
            \Log::error('Error getting top accuracy performers: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Calculate student improvement percentage based on recent vs older performance
     */
    private function calculateStudentImprovement($userId)
    {
        try {
            // Get scores from last 30 days
            $recentScores = DB::table('assessment_sessions')
                ->where('user_id', $userId)
                ->where('status', 'completed')
                ->where('completed_at', '>=', Carbon::now()->subDays(30))
                ->whereNotNull('accuracy_percentage')
                ->avg('accuracy_percentage');
            
            // Get scores from 31-60 days ago
            $olderScores = DB::table('assessment_sessions')
                ->where('user_id', $userId)
                ->where('status', 'completed')
                ->where('completed_at', '>=', Carbon::now()->subDays(60))
                ->where('completed_at', '<', Carbon::now()->subDays(30))
                ->whereNotNull('accuracy_percentage')
                ->avg('accuracy_percentage');
            
            if ($olderScores && $olderScores > 0) {
                $improvement = round((($recentScores - $olderScores) / $olderScores) * 100, 1);
                return $improvement >= 0 ? "+{$improvement}%" : "{$improvement}%";
            }
            
            return '+0%';
        } catch (\Exception $e) {
            \Log::error("Error calculating improvement for user {$userId}: " . $e->getMessage());
            return '+0%';
        }
    }

    /**
     * Get recent activity from multiple sources
     * FIXED: Now includes teacher-created assessments AND system assessments
     */
    private function getRecentActivity($teacherSections)
    {
        try {
            $activities = collect([]);
            
            // 1. Get recent quiz results (teacher-created assessments)
            $quizActivities = DB::table('quiz_results')
                ->join('student_profile', 'quiz_results.student_id', '=', 'student_profile.user_id')
                ->join('teacher_assessments', 'quiz_results.assessment_id', '=', 'teacher_assessments.id')
                ->whereIn('student_profile.section', $teacherSections)
                ->select(
                    'quiz_results.completed_at',
                    'teacher_assessments.title as assessment_title',
                    'student_profile.firstname',
                    'student_profile.lastname',
                    'student_profile.section',
                    'quiz_results.percentage as score',
                    DB::raw("'teacher_assessment' as source_type")
                )
                ->orderBy('quiz_results.completed_at', 'desc')
                ->limit(10)
                ->get();
            
            
            // 3. Get recent assessment sessions
            $sessionActivities = DB::table('assessment_sessions')
                ->join('student_profile', 'assessment_sessions.user_id', '=', 'student_profile.user_id')
                ->whereIn('student_profile.section', $teacherSections)
                ->where('assessment_sessions.status', 'completed')
                ->whereNotNull('assessment_sessions.accuracy_percentage')
                ->select(
                    'assessment_sessions.completed_at',
                    DB::raw("CONCAT(
                        UPPER(SUBSTRING(assessment_sessions.session_type, 1, 1)),
                        SUBSTRING(assessment_sessions.session_type, 2),
                        ' - ',
                        UPPER(SUBSTRING(assessment_sessions.competency, 1, 1)),
                        REPLACE(SUBSTRING(assessment_sessions.competency, 2), '_', ' ')
                    ) as assessment_title"),
                    'student_profile.firstname',
                    'student_profile.lastname',
                    'student_profile.section',
                    'assessment_sessions.accuracy_percentage as score',
                    DB::raw("'assessment_session' as source_type")
                )
                ->orderBy('assessment_sessions.completed_at', 'desc')
                ->limit(10)
                ->get();
            
            // Merge all activities
            $activities = $activities
                ->concat($quizActivities)
                ->concat($sessionActivities);
            
            // Sort by completed_at descending and take top 4
            $recentActivity = $activities
                ->sortByDesc('completed_at')
                ->take(4);

            return $recentActivity->map(function($activity) {
                return [
                    'type' => $activity->source_type === 'teacher_assessment' ? 'quiz_completed' : 'assessment_completed',
                    'title' => $activity->assessment_title . ' completed by ' . $activity->firstname . ' ' . $activity->lastname,
                    'section' => $activity->section,
                    'score' => round($activity->score, 1),
                    'time' => Carbon::parse($activity->completed_at)->diffForHumans(),
                    'icon' => $activity->source_type === 'teacher_assessment' ? 'quiz' : 'assessment'
                ];
            })->values();
            
        } catch (\Exception $e) {
            \Log::error('Error getting recent activity: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get top performing section
     * FIXED: Now based on getSectionAveragePerformance calculations
     */
    private function getTopPerformingSection($teacherSections)
    {
        $sectionStats = $this->getSectionPerformance($teacherSections);
        return !empty($sectionStats) ? $sectionStats[0] : null;
    }

    /**
     * Get worst performing section
     * FIXED: Now based on getSectionAveragePerformance calculations
     */
    private function getWorstPerformingSection($teacherSections)
    {
        $sectionStats = $this->getSectionPerformance($teacherSections);
        return !empty($sectionStats) ? end($sectionStats) : null;
    }

    /**
     * Get overall accuracy
     */
    private function getOverallAccuracy($teacherSections)
    {
        try {
            $scores = [];
            
            // Get quiz_results scores
            $quizScores = DB::table('quiz_results')
                ->join('student_profile', 'quiz_results.student_id', '=', 'student_profile.user_id')
                ->whereIn('student_profile.section', $teacherSections)
                ->pluck('quiz_results.percentage')
                ->toArray();
            
            $scores = array_merge($scores, $quizScores);
            
            // Get assessment_sessions scores
            $sessionScores = DB::table('assessment_sessions')
                ->join('student_profile', 'assessment_sessions.user_id', '=', 'student_profile.user_id')
                ->whereIn('student_profile.section', $teacherSections)
                ->where('assessment_sessions.status', 'completed')
                ->whereNotNull('assessment_sessions.accuracy_percentage')
                ->pluck('assessment_sessions.accuracy_percentage')
                ->toArray();
            
            $scores = array_merge($scores, $sessionScores);
            
            return count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : 0;
            
        } catch (\Exception $e) {
            \Log::error('Error getting overall accuracy: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get section color based on performance
     */
    private function getSectionColor($score)
    {
        if ($score >= 85) return 'green';
        if ($score >= 75) return 'blue';
        if ($score >= 65) return 'yellow';
        return 'red';
    }

    /**
     * Get default dashboard data when no sections are found
     */
    private function getDefaultDashboardData()
    {
        return [
            'totalStudents' => ['count' => 0, 'growth' => 0, 'growthText' => '0%'],
            'activeAssessments' => ['count' => 0, 'growth' => 0, 'growthText' => '0'],
            'averageScore' => ['score' => 0, 'growth' => 0, 'growthText' => '0%'],
            'responseTime' => ['time' => 0, 'improvement' => 0, 'improvementText' => '0 min'],
            'sectionPerformance' => [],
            'topPerformers' => [],
            'recentActivity' => [],
            'topPerformingSection' => null,
            'worstPerformingSection' => null,
            'overallAccuracy' => 0,
        ];
    }
}