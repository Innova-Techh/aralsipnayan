<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class TeacherSectionExportController extends Controller
{
    /**
     * Export section students data as CSV
     */
    public function exportSectionCSV($section)
    {
        $teacher = Auth::guard('admin')->user();
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();

        if (!$teacherProfile) {
            abort(404, 'Teacher profile not found');
        }

        // Verify teacher has access to this section
        $hasAccess = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->where('section', $section)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Access denied to this section');
        }

        // Get all students data for this section
        $studentsData = $this->getSectionStudentsData($section);

        // Generate CSV
        $filename = $section . '_students_performance_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($studentsData) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Student ID',
                'Name',
                'Email',
                'Section',
                'Rank',
                'Total Points',
                'Overall Score (%)',
                'Number & Algebra Score (%)',
                'Measurement & Geometry Score (%)',
                'Data & Probability Score (%)',
                'Diagnostics Completed',
                'Regular Assessments',
                'Best Streak',
                'Current Streak',
                'Time Spent (hours)',
                'Avg Time per Question (min)',
                'Last Assessment Name',
                'Last Assessment Score (%)',
                'Last Assessment Date',
                // Needs Improvement
                'Needs Improvement',
                // Competency Details
                'Number & Algebra - Accuracy (%)',
                'Number & Algebra - BKT Score (%)',
                'Number & Algebra - Avg Response Time (min)',
                'Number & Algebra - Difficulty Level',
                'Measurement & Geometry - Accuracy (%)',
                'Measurement & Geometry - BKT Score (%)',
                'Measurement & Geometry - Avg Response Time (min)',
                'Measurement & Geometry - Difficulty Level',
                'Data & Probability - Accuracy (%)',
                'Data & Probability - BKT Score (%)',
                'Data & Probability - Avg Response Time (min)',
                'Data & Probability - Difficulty Level',
                // Struggling Areas
                'Struggling Areas (Topic: Score% - Attempts)',
                'Achievements Count'
            ]);

            // Add data rows
            foreach ($studentsData as $student) {
                // Format needs improvement
                $needsImprovement = implode(', ', $student['needs_improvement']);
                
                // Format competency details
                $compDetails = [];
                foreach ($student['competency_details'] as $comp) {
                    $compDetails[] = [
                        'accuracy' => $comp['accuracy'],
                        'bkt_score' => $comp['bkt_score'],
                        'avg_response_time' => $comp['avg_response_time'],
                        'difficulty' => $comp['current_difficulty']
                    ];
                }
                
                // Format struggling areas
                $strugglingAreasText = [];
                foreach ($student['struggling_areas'] as $area) {
                    $strugglingAreasText[] = $area['topic'] . ': ' . $area['score'] . '% - ' . $area['attempts'] . ' attempts';
                }
                $strugglingAreasFormatted = empty($strugglingAreasText) ? 'None' : implode(' | ', $strugglingAreasText);
                
                fputcsv($file, [
                    $student['student_id'] ?? 'N/A',
                    $student['name'],
                    $student['email'],
                    $student['section'],
                    $student['section_rank'] ?? 'N/A',
                    $student['total_points'],
                    $student['overall_score'],
                    $student['numerical_literacy_score'],
                    $student['geometric_reasoning_score'],
                    $student['problem_solving_score'],
                    $student['diagnostic_count'] . '/3',
                    $student['regular_count'],
                    $student['best_streak'],
                    $student['current_streak'],
                    $student['time_spent'],
                    $student['avg_time_per_question'],
                    $student['last_assessment_name'],
                    $student['last_assessment_score'],
                    $student['last_assessment_date'] ?? 'N/A',
                    // Needs Improvement
                    $needsImprovement,
                    // Competency Details (3 competencies x 4 fields each)
                    $compDetails[0]['accuracy'] ?? 0,
                    $compDetails[0]['bkt_score'] ?? 0,
                    $compDetails[0]['avg_response_time'] ?? 0,
                    $compDetails[0]['difficulty'] ?? 'Beginner',
                    $compDetails[1]['accuracy'] ?? 0,
                    $compDetails[1]['bkt_score'] ?? 0,
                    $compDetails[1]['avg_response_time'] ?? 0,
                    $compDetails[1]['difficulty'] ?? 'Beginner',
                    $compDetails[2]['accuracy'] ?? 0,
                    $compDetails[2]['bkt_score'] ?? 0,
                    $compDetails[2]['avg_response_time'] ?? 0,
                    $compDetails[2]['difficulty'] ?? 'Beginner',
                    // Struggling Areas
                    $strugglingAreasFormatted,
                    $student['achievements_count']
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export section students data as Excel
     */
    public function exportSectionExcel($section)
    {
        $teacher = Auth::guard('admin')->user();
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();

        if (!$teacherProfile) {
            abort(404, 'Teacher profile not found');
        }

        // Verify teacher has access to this section
        $hasAccess = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->where('section', $section)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Access denied to this section');
        }

        // Get all students data for this section
        $studentsData = $this->getSectionStudentsData($section);

        // Create Excel export using Laravel Excel
        $filename = $section . '_students_performance_' . date('Y-m-d') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SectionStudentsExport($studentsData, $section),
            $filename
        );
    }

    /**
     * Get comprehensive student data for a section
     */
    private function getSectionStudentsData($section)
    {
        // Get all students in the section
        $students = DB::table('student_profile')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->leftJoin('user_progress', 'student_profile.user_id', '=', 'user_progress.user_id')
            ->where('student_profile.section', $section)
            ->select([
                'student_profile.user_id',
                'student_profile.student_id',
                'student_profile.firstname',
                'student_profile.lastname',
                'student_profile.section',
                'student_profile.current_streak',
                'student_profile.longest_streak',
                'users.email',
                'users.status',
                DB::raw('COALESCE(user_progress.total_points, student_profile.total_points, 0) as total_points')
            ])
            ->orderByDesc(DB::raw('COALESCE(user_progress.total_points, student_profile.total_points, 0)'))
            ->get();

        $studentsData = [];
        $rank = 1;

        foreach ($students as $student) {
            $studentId = $student->user_id;
            
            // Get mastery data
            $masteryData = DB::table('student_mastery')
                ->where('user_id', $studentId)
                ->get();
            
            // Calculate competency scores
            $competencyScores = [
                'numerical_literacy_score' => 0,
                'geometric_reasoning_score' => 0,
                'problem_solving_score' => 0
            ];
            
            foreach ($masteryData as $mastery) {
                $score = $mastery->final_mastery_score ?? 0;
                
                switch ($mastery->competency) {
                    case 'number_algebra':
                        $competencyScores['numerical_literacy_score'] = round($score);
                        break;
                    case 'measurement_geometry':
                        $competencyScores['geometric_reasoning_score'] = round($score);
                        break;
                    case 'data_probability':
                        $competencyScores['problem_solving_score'] = round($score);
                        break;
                }
            }
            
            // Get assessment statistics
            $assessmentStats = $this->getAssessmentStatistics($studentId);
            
            // Get regular assessment IDs
            $regularAssessmentIds = $this->getRegularAssessmentIds($studentId);
            
            // Calculate overall score and time metrics
            if (!empty($regularAssessmentIds)) {
                $regularMasteryData = DB::table('student_mastery')
                    ->where('user_id', $studentId)
                    ->whereIn('competency', ['number_algebra', 'measurement_geometry', 'data_probability'])
                    ->get();
                
                $regularCompetencyScores = [];
                foreach ($regularMasteryData as $mastery) {
                    $score = $mastery->final_mastery_score ?? 0;
                    if ($score > 0) {
                        $regularCompetencyScores[] = round($score);
                    }
                }
                
                $overallScore = count($regularCompetencyScores) > 0 
                    ? round(array_sum($regularCompetencyScores) / count($regularCompetencyScores), 1) 
                    : 0;
                
                $regularTimeSeconds = DB::table('question_responses')
                    ->where('user_id', $studentId)
                    ->whereIn('assessment_id', $regularAssessmentIds)
                    ->sum('response_time');
                
                $regularQuestions = DB::table('question_responses')
                    ->where('user_id', $studentId)
                    ->whereIn('assessment_id', $regularAssessmentIds)
                    ->count();
                
                $timeSpent = round($regularTimeSeconds / 3600, 1);
                $avgTimePerQuestion = $regularQuestions > 0 
                    ? round(($regularTimeSeconds / $regularQuestions) / 60, 1) 
                    : 0;
            } else {
                $overallScore = 0;
                $timeSpent = 0;
                $avgTimePerQuestion = 0;
            }
            
            // Get last assessment info
            $lastAssessment = $this->getLastAssessment($studentId, $regularAssessmentIds);
            
            // Get detailed struggling areas
            $strugglingAreas = $this->getStrugglingAreas($studentId);
            
            // Get detailed competency breakdown
            $competencyDetails = $this->getCompetencyDetails($studentId);
            
            // Get achievements count
            $achievementsCount = DB::table('user_badges')
                ->where('user_id', $studentId)
                ->count();
            
            // Determine needs improvement categories
            $needsImprovement = $this->getNeedsImprovementCategories($overallScore, $competencyScores);
            
            // Only assign rank to active students
            $sectionRank = ($student->status === 'active') ? $rank : null;
            if ($student->status === 'active') {
                $rank++;
            }
            
            $studentsData[] = [
                'student_id' => $student->student_id,
                'name' => trim($student->firstname . ' ' . $student->lastname),
                'email' => $student->email,
                'section' => $student->section,
                'section_rank' => $sectionRank,
                'total_points' => $student->total_points,
                'overall_score' => $overallScore,
                'numerical_literacy_score' => $competencyScores['numerical_literacy_score'],
                'geometric_reasoning_score' => $competencyScores['geometric_reasoning_score'],
                'problem_solving_score' => $competencyScores['problem_solving_score'],
                'diagnostic_count' => $assessmentStats['diagnostic_count'],
                'regular_count' => $assessmentStats['regular_count'],
                'best_streak' => $student->longest_streak ?? 0,
                'current_streak' => $student->current_streak ?? 0,
                'time_spent' => $timeSpent,
                'avg_time_per_question' => $avgTimePerQuestion,
                'last_assessment_name' => $lastAssessment['name'] ?? 'No assessments yet',
                'last_assessment_score' => $lastAssessment['score'] ?? 0,
                'last_assessment_date' => $lastAssessment['date'] ?? null,
                'struggling_areas' => $strugglingAreas,
                'competency_details' => $competencyDetails,
                'needs_improvement' => $needsImprovement,
                'achievements_count' => $achievementsCount
            ];
        }

        return $studentsData;
    }

    /**
     * Get assessment statistics for a student
     */
    private function getAssessmentStatistics($studentId)
    {
        try {
            $allAssessmentIds = DB::table('question_responses')
                ->where('user_id', $studentId)
                ->distinct()
                ->pluck('assessment_id')
                ->toArray();
            
            $regularCount = 0;
            $diagnosticCount = 0;
            
            foreach ($allAssessmentIds as $assessmentId) {
                $isDiagnostic = DB::table('diagnostic_sessions')
                    ->where('session_id', $assessmentId)
                    ->where('user_id', $studentId)
                    ->where('status', 'completed')
                    ->exists();
                
                if ($isDiagnostic) {
                    $diagnosticCount++;
                } else {
                    $isRegular = DB::table('assessments')
                        ->where('assessment_id', $assessmentId)
                        ->where('user_id', $studentId)
                        ->where('status', 'completed')
                        ->where('assessment_type', 'regular')
                        ->exists();
                    
                    if (!$isRegular) {
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
            
            return [
                'regular_count' => $regularCount,
                'diagnostic_count' => $diagnosticCount
            ];
        } catch (\Exception $e) {
            return [
                'regular_count' => 0,
                'diagnostic_count' => 0
            ];
        }
    }

    /**
     * Get all regular assessment IDs (excluding diagnostics)
     */
    private function getRegularAssessmentIds($studentId)
    {
        $allAssessmentIds = DB::table('question_responses')
            ->where('user_id', $studentId)
            ->distinct()
            ->pluck('assessment_id')
            ->toArray();
        
        $regularIds = [];
        
        foreach ($allAssessmentIds as $assessmentId) {
            $isDiagnostic = DB::table('diagnostic_sessions')
                ->where('session_id', $assessmentId)
                ->where('user_id', $studentId)
                ->where('status', 'completed')
                ->exists();
            
            if (!$isDiagnostic) {
                $isRegular = DB::table('assessments')
                    ->where('assessment_id', $assessmentId)
                    ->where('user_id', $studentId)
                    ->where('status', 'completed')
                    ->where('assessment_type', 'regular')
                    ->exists();
                
                if (!$isRegular) {
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
     * Get the last assessment taken by student
     */
    private function getLastAssessment($studentId, $regularAssessmentIds)
    {
        try {
            if (empty($regularAssessmentIds)) {
                return null;
            }
            
            $mostRecentResponse = DB::table('question_responses')
                ->where('user_id', $studentId)
                ->whereIn('assessment_id', $regularAssessmentIds)
                ->orderByDesc('answered_at')
                ->first();
            
            if (!$mostRecentResponse) {
                return null;
            }
            
            $assessmentId = $mostRecentResponse->assessment_id;
            
            $assessmentData = DB::table('assessments')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $studentId)
                ->where('status', 'completed')
                ->where('assessment_type', 'regular')
                ->first();
            
            if (!$assessmentData) {
                $assessmentData = DB::table('assessment_sessions')
                    ->where('session_id', $assessmentId)
                    ->where('user_id', $studentId)
                    ->where('status', 'completed')
                    ->first();
            }
            
            if (!$assessmentData) {
                return null;
            }
            
            $responses = DB::table('question_responses')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $studentId)
                ->get();
            
            $totalQuestions = $responses->count();
            $correctAnswers = $responses->where('is_correct', 1)->count();
            
            $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
            
            $competencyName = ucfirst(str_replace('_', ' & ', $assessmentData->competency ?? 'Assessment'));
            $completedAt = $mostRecentResponse->answered_at;
            
            return [
                'name' => $competencyName . ' Assessment',
                'score' => $score,
                'date' => date('Y-m-d', strtotime($completedAt))
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get detailed struggling areas for a student
     */
    private function getStrugglingAreas($studentId)
    {
        try {
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
            
            $strugglingAreas = $topicPerformance->filter(function($topic) {
                $percentage = $topic->total > 0 ? ($topic->correct / $topic->total) * 100 : 0;
                return $percentage < 70 && $topic->attempts >= 3;
            })->map(function($topic) {
                $percentage = $topic->total > 0 ? ($topic->correct / $topic->total) * 100 : 0;
                
                return [
                    'topic' => ucfirst(str_replace('_', ' ', $topic->topic ?? 'General')),
                    'area' => ucfirst(str_replace('_', ' & ', $topic->area)),
                    'score' => round($percentage, 1),
                    'attempts' => $topic->attempts
                ];
            })->values()->toArray();
            
            return $strugglingAreas;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get detailed competency breakdown for a student
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
                        'question_responses.response_time'
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
                
                $bktScore = $masteryData ? round(($masteryData->bkt_score ?? 0) * 100, 1) : 0;
                $currentDifficulty = $masteryData ? $masteryData->current_difficulty : 'beginner';
                
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
                
                $competencyDetails[] = [
                    'competency' => $displayName,
                    'accuracy' => $accuracy,
                    'bkt_score' => $bktScore,
                    'avg_response_time' => $avgResponseTime,
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $correctAnswers,
                    'current_difficulty' => ucfirst($currentDifficulty)
                ];
            }
            
            return $competencyDetails;
            
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Determine needs improvement categories based on scores
     */
    private function getNeedsImprovementCategories($overallScore, $competencyScores)
    {
        $needsImprovement = [];
        
        // Check overall performance
        if ($overallScore < 60) {
            $needsImprovement[] = 'Overall Performance';
        }
        
        // Check individual competencies
        if ($competencyScores['numerical_literacy_score'] < 60) {
            $needsImprovement[] = 'Number & Algebra';
        }
        if ($competencyScores['geometric_reasoning_score'] < 60) {
            $needsImprovement[] = 'Measurement & Geometry';
        }
        if ($competencyScores['problem_solving_score'] < 60) {
            $needsImprovement[] = 'Data & Probability';
        }
        
        return empty($needsImprovement) ? ['None'] : $needsImprovement;
    }
}
