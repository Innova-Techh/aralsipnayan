<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAssignment;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherSectionController extends Controller
{
    

    /**
     * Display the section management page with real data
     */
    public function index()
    {
        $teacher = Auth::guard('admin')->user();
        
        // Get teacher's sections with statistics
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        
        if (!$teacherProfile) {
            $sectionsData = [];
        } else {
            // Get sections assigned to this teacher from sections table
            $teacherSections = DB::table('sections')
                ->join('teacher_sections', 'sections.name', '=', 'teacher_sections.section')
                ->where('teacher_sections.teacher_id', $teacherProfile->id)
                ->where('sections.is_active', true)
                ->select('sections.name', 'sections.grade_level', 'sections.school_year')
                ->get();

            $sectionsData = [];
            
            foreach ($teacherSections as $section) {

                // Get student active count for this section
                $studentActiveCount = DB::table('student_profile')
                    ->join('users', 'student_profile.user_id', '=', 'users.id')
                    ->where('student_profile.section', $section->name)
                    ->where('users.status', 'active')
                    ->count();
                // Get student count for this section
                $studentCount = DB::table('student_profile')
                    ->join('users', 'student_profile.user_id', '=', 'users.id')
                    ->where('student_profile.section', $section->name)
                    ->count();

                // Get active assessments count for this section
                $activeAssessmentsCount = DB::table('teacher_assessment_assignments')
                    ->join('teacher_assessments', 'teacher_assessment_assignments.assessment_id', '=', 'teacher_assessments.id')
                    ->where('teacher_assessment_assignments.section', $section->name)
                    ->where('teacher_assessments.status', 'Active')
                    ->where('teacher_assessments.created_by', $teacher->id)
                    ->count();

                // Get average performance for this section
                $averagePerformance = $this->getSectionAveragePerformance($section->name);

                $sectionsData[] = [
                    'section' => $section->name,
                    'grade_level' => $section->grade_level,
                    'student_count' => $studentCount,
                    'student_active' => $studentActiveCount,
                    'active_assessments' => $activeAssessmentsCount,
                    'average_performance' => $averagePerformance,
                    'last_activity' => $this->getLastActivityForSection($section->name)
                ];
            }
        }
      

        return view('admin.teacher.sections.index', compact('sectionsData'));
    }

    /**
     * Show detailed section information with students
     */
    public function show($section)
    {
       
        $teacher = Auth::guard('admin')->user();
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();

        if (!$teacherProfile) {
            abort(404, 'Teacher profile not found');
        }
        // Verify teacher has access to this section
        $teacherSections = DB::table('sections')
            ->join('teacher_sections', 'sections.name', '=', 'teacher_sections.section')
            ->where('teacher_sections.teacher_id', $teacherProfile->id)
            ->where('sections.is_active', true)
            ->pluck('sections.name')
            ->toArray();

        if (!in_array($section, $teacherSections)) {
            abort(403, 'Access denied to this section');
        }

        // Get section details from sections table
        $sectionDetails = DB::table('sections')
            ->where('name', $section)
            ->where('is_active', true)
            ->first();

        // Get students with detailed information from user_progress table
        $students = DB::table('student_profile')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->leftJoin('user_progress', 'student_profile.user_id', '=', 'user_progress.user_id')
            ->where('student_profile.section', $section)
            ->select([
                'student_profile.user_id',
                'student_profile.student_id',
                'student_profile.firstname',
                'student_profile.middlename',
                'student_profile.lastname',
                'student_profile.section',
                'student_profile.last_activity_date',
                'users.email',
                'users.status',
                'user_progress.total_points',
                'user_progress.current_level',
                'user_progress.points_in_current_level',
                'user_progress.current_rank',
                'user_progress.current_streak',
                'user_progress.last_assessment_date'
            ])
            ->orderByRaw("CASE WHEN users.status = 'active' THEN 1 ELSE 2 END ASC")
            ->orderByDesc('user_progress.total_points')
            ->get()
            ->map(function ($student, $index) use ($section) {
                // Get assessment data for score calculation
                $avgScore = 0;

                try {
                    // Get average score from assessments table (same as index page)
                    $avgScore = DB::table('assessments')
                        ->where('assessments.user_id', $student->user_id)
                        ->where('assessments.status', 'completed')
                        ->where('assessments.assessment_type', 'regular')
                        ->avg('assessments.accuracy_percentage');

                } catch (\Exception $e) {
                    // If tables don't exist, use default values
                    $avgScore = 0;
                }

                // If no assessment data, set score to 0 (will be excluded from average)
                if (!$avgScore) {
                    $avgScore = 0; // Don't use fallback formula
                }

                return [
                    'id' => $student->user_id,
                    'student_id' => $student->student_id,
                    'name' => trim($student->firstname . ' ' . ($student->middlename ? $student->middlename . ' ' : '') . $student->lastname),
                    'email' => $student->email,
                    'status' => $student->status,
                    'score' => round($avgScore),
                    'current_level' => $student->current_level ?? 1,
                    'points_in_current_level' => $student->points_in_current_level ?? 0,
                    'current_rank' => $student->current_rank ?? 'Math Explorer',
                    'points' => $student->total_points ?? 0,
                    'streak' => $student->current_streak ?? 0,
                    'last_activity' => $student->last_activity_date ? 
                        \Carbon\Carbon::parse($student->last_activity_date)->diffForHumans() : 
                        'Never',
                    'rank' => $index + 1
                ];
            });
        // Get student active count for this section
        $studentActiveCount = DB::table('student_profile')
        ->join('users', 'student_profile.user_id', '=', 'users.id')
        ->join('sections', 'student_profile.section', '=', 'sections.name')
        ->where('sections.name', $section)
        ->where('sections.is_active', true)
        ->where('users.status', 'active')
        ->count();

        // Get total student count for this section
        $studentCount = DB::table('student_profile')
        ->join('users', 'student_profile.user_id', '=', 'users.id')
        ->join('sections', 'student_profile.section', '=', 'sections.name')
        ->where('sections.name', $section)
        ->where('sections.is_active', true)
        ->count();
        
        $averageScore = $this->getSectionAveragePerformance($section);
        
        // After building $students collection
        $activeStudents = $students->filter(fn($student) => $student['status'] === 'active');

        // Sort active students by points
        $sortedActive = $activeStudents->sortByDesc('points')->values();

        // Recalculate top performer
        $topPerformer = $sortedActive->first();
        $topPerformerName = $topPerformer ? $topPerformer['name'] : 'N/A';
        $topPerformerScore = $topPerformer ? $topPerformer['score'] : 0;

        // Also recompute average level (active only)
        $averageLevel = $activeStudents->isNotEmpty()
            ? round($activeStudents->avg('current_level'))
            : 1;

        // Get teacher name
        $teacherName = $teacher->firstname . ' ' . $teacher->lastname;

        // Get section room (you can customize this based on your database)
        $room = 'Room 201'; // Default or fetch from database

        // Format section name nicely
        $formattedSectionName = 'Mathematics 101 - Section ' . strtoupper($section);

        $sectionData = [
            'name' => $formattedSectionName,
            'raw_name' => $section,
            'section_id' => 'MATH101-' . strtoupper(substr($section, -1)),
        ];

        return view('admin.teacher.sections.manage-section', [
            'section' => $sectionData,
            'students' => $students,
            'totalStudents' => $studentCount,
            'activeStudents' => $studentActiveCount,
            'averageScore' => $averageScore,
            'averageLevel' => $averageLevel,
            'topPerformer' => $topPerformerName,
            'topPerformerScore' => $topPerformerScore,
            'teacher' => $teacherName,
            'room' => $room,
            'sectionId' => $sectionData['section_id']
        ]);
    }

    /**
     * Get students for a specific section
     */
    public function getSectionStudents($section)
    {
        $teacher = Auth::guard('admin')->user();
        
        // Verify teacher has access to this section
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        $teacherSections = DB::table('sections')
            ->join('teacher_sections', 'sections.name', '=', 'teacher_sections.section')
            ->where('teacher_sections.teacher_id', $teacherProfile->id)
            ->where('sections.is_active', true)
            ->pluck('sections.name')
            ->toArray();

        if (!in_array($section, $teacherSections)) {
            return response()->json(['error' => 'Access denied to this section'], 403);
        }

        // Get students from the section with their progress data from user_progress table
        $students = DB::table('student_profile')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->leftJoin('user_progress', 'student_profile.user_id', '=', 'user_progress.user_id')
            ->where('student_profile.section', $section)
            ->where('users.status', 'active')
            ->select([
                'student_profile.user_id',
                'student_profile.student_id',
                'student_profile.firstname',
                'student_profile.middlename',
                'student_profile.lastname',
                'student_profile.section',
                'student_profile.gender',
                'student_profile.grade_level',
                'student_profile.school_year',
                'student_profile.last_activity_date',
                'users.email',
                'user_progress.total_points',
                'user_progress.current_streak',
                'user_progress.last_assessment_date'
            ])
            ->get()
            ->map(function ($student) {
                // Calculate progress percentage (simplified - you can enhance this)
                $progress = min(($student->total_points / 1000) * 100, 100);
                
                return [
                    'user_id' => $student->user_id,
                    'student_id' => $student->student_id,
                    'firstname' => $student->firstname,
                    'middlename' => $student->middlename,
                    'lastname' => $student->lastname,
                    'name' => trim($student->firstname . ' ' . ($student->middlename ? $student->middlename . ' ' : '') . $student->lastname),
                    'email' => $student->email,
                    'gender' => $student->gender,
                    'section' => $student->section,
                    'grade_level' => $student->grade_level,
                    'school_year' => $student->school_year,
                    'progress' => round($progress, 1),
                    'total_points' => $student->total_points ?? 0,
                    'current_streak' => $student->current_streak ?? 0,
                    'last_activity' => $student->last_activity_date ? 
                        \Carbon\Carbon::parse($student->last_activity_date)->diffForHumans() : 
                        'Never'
                ];
            });

        return response()->json([
            'students' => $students,
            'section' => $section
        ]);
    }

    /**
     * Update a section
     */
    public function update(Request $request, $section)
    {

        $teacher = Auth::guard('admin')->user();
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();

        if (!$teacherProfile) {
            return response()->json(['error' => 'Teacher profile not found'], 404);
        }

        try {
            DB::beginTransaction();

            // Update the section in sections table
            DB::table('sections')
                ->where('name', $section)
                ->update([
                    'name' => $request->section,
                    'grade_level' => '6', // Fixed to Grade 6
                    'updated_at' => now()
                ]);

            // Update all teacher_sections records with the old section name
            DB::table('teacher_sections')
                ->where('section', $section)
                ->update([
                    'section' => $request->section,
                    'grade_level' => '6', // Fixed to Grade 6
                    'school_year' => '2024-2025',
                    'updated_at' => now()
                ]);
            

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Section updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update section: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Deactivate a section instead of deleting it
     */
    public function deactivate($section)
    {
        $teacher = Auth::guard('admin')->user();
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();

        if (!$teacherProfile) {
            return response()->json(['error' => 'Teacher profile not found'], 404);
        }

        try {
            DB::beginTransaction();

            // Check if this teacher manages the section
            $assignedSection = DB::table('teacher_sections')
                ->where('teacher_id', $teacherProfile->id)
                ->where('section', $section)
                ->exists();

            if (!$assignedSection) {
                return response()->json([
                    'error' => 'You are not authorized to modify this section.'
                ], 403);
            }

            // Check if section exists
            $sectionRecord = DB::table('sections')->where('name', $section)->first();
            if (!$sectionRecord) {
                return response()->json(['error' => 'Section not found'], 404);
            }

            // Deactivate the section
            DB::table('sections')
                ->where('name', $section)
                ->update([
                    'is_active' => false,
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Section has been deactivated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to deactivate section: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get average performance for a section from user_progress table
     */
    private function getSectionAveragePerformance($section)
    {
        
        try {
            // First, let's check what tables exist and what data is available
            
            // Check if assessments table exists and has data
            $assessmentsCount = 0;
            if (DB::getSchemaBuilder()->hasTable('assessments')) {
                $assessmentsCount = DB::table('assessments')->count();
                
                // Check assessments for this section
                $sectionAssessments = DB::table('assessments')
                    ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                    ->where('student_profile.section', $section)
                    ->count();
                
                // Check completed assessments
                $completedAssessments = DB::table('assessments')
                    ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                    ->where('student_profile.section', $section)
                    ->where('assessments.status', 'completed')
                    ->count();
            } else {
            }
            
            // Check user_progress table
            $userProgressCount = 0;
            if (DB::getSchemaBuilder()->hasTable('user_progress')) {
                $userProgressCount = DB::table('user_progress')->count();
                
                // Check user_progress for this section
                $sectionProgress = DB::table('student_profile')
                    ->join('user_progress', 'student_profile.user_id', '=', 'user_progress.user_id')
                    ->where('student_profile.section', $section)
                    ->count();
            } else {
            }
            
            // Get students in this section
            $students = DB::table('student_profile')
                ->join('users', 'student_profile.user_id', '=', 'users.id')
                ->where('student_profile.section', $section)
                ->where('users.status', 'active')
                ->pluck('student_profile.user_id');
            
            if ($students->isEmpty()) {
                return 0;
            }
            
            // Calculate average score per student, then average those
            $studentAverages = [];
            foreach ($students as $studentId) {
                $avgScore = DB::table('assessments')
                    ->join('users', 'assessments.user_id', '=', 'users.id')
                    ->where('assessments.user_id', $studentId)
                    ->where('users.status', 'active')
                    ->where('assessments.status', 'completed')
                    ->where('assessments.assessment_type', 'regular')
                    ->avg('assessments.accuracy_percentage');
                
                if ($avgScore) {
                    $studentAverages[] = $avgScore;
                }
            }
            
            $avgAccuracy = !empty($studentAverages) ? array_sum($studentAverages) / count($studentAverages) : 0;
          
            
            $result = $avgAccuracy ? round($avgAccuracy, 1) : 0;
            Log::warning('Average Score From Section Controller');
            Log::warning($avgAccuracy);
            Log::warning($result);
            return $result;
            
        } catch (\Exception $e) {
            
            // Fallback to user_progress total_points if assessment data is not available
            try {
                $avgPoints = DB::table('student_profile')
                    ->join('user_progress', 'student_profile.user_id', '=', 'user_progress.user_id')
                    ->where('student_profile.section', $section)
                    ->avg('user_progress.total_points');
                    
                $fallbackResult = $avgPoints ? round($avgPoints, 1) : 0;
                
                return $fallbackResult;
            } catch (\Exception $fallbackError) {
                return 0;
            }
        }
    }

    /**
     * Get last activity for a section from user_progress table
     */
    private function getLastActivityForSection($section)
    {
        $lastActivity = DB::table('student_profile')
            ->join('user_progress', 'student_profile.user_id', '=', 'user_progress.user_id')
            ->where('student_profile.section', $section)
            ->whereNotNull('user_progress.last_assessment_date')
            ->max('user_progress.last_assessment_date');
            
        return $lastActivity ? \Carbon\Carbon::parse($lastActivity)->diffForHumans() : 'No activity';
    }

/**
 * Format time in seconds to readable format
 */
private function formatTime($seconds)
{
    $minutes = floor($seconds / 60);
    $secs = $seconds % 60;
    
    if ($minutes > 0) {
        return $minutes . ' min ' . $secs . ' sec';
    }
    return $secs . ' sec';
}
}