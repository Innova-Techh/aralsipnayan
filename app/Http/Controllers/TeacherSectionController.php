<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAssignment;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $teacherSections = DB::table('teacher_sections')
                ->where('teacher_id', $teacherProfile->id)
                ->pluck('section')
                ->toArray();

            $sectionsData = [];
            
            foreach ($teacherSections as $section) {
                // Get student count for this section
                $studentCount = DB::table('student_profile')
                    ->join('users', 'student_profile.user_id', '=', 'users.id')
                    ->where('student_profile.section', $section)
                    ->where('users.status', 'active')
                    ->count();

                // Get active assessments count for this section
                $activeAssessmentsCount = DB::table('teacher_assessment_assignments')
                    ->join('teacher_assessments', 'teacher_assessment_assignments.assessment_id', '=', 'teacher_assessments.id')
                    ->where('teacher_assessment_assignments.section', $section)
                    ->where('teacher_assessments.status', 'Active')
                    ->where('teacher_assessments.created_by', $teacher->id)
                    ->count();

                // Get average performance for this section
                $averagePerformance = $this->getSectionAveragePerformance($section);

                $sectionsData[] = [
                    'section' => $section,
                    'grade_level' => '6', // Fixed to Grade 6
                    'student_count' => $studentCount,
                    'active_assessments' => $activeAssessmentsCount,
                    'average_performance' => $averagePerformance,
                    'last_activity' => $this->getLastActivityForSection($section)
                ];
            }
        }

        return view('admin.teacher.sections.index', compact('sectionsData'));
    }

    /**
     * Get students for a specific section
     */
    public function getSectionStudents($section)
    {
        $teacher = Auth::guard('admin')->user();
        
        // Verify teacher has access to this section
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        $teacherSections = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->pluck('section')
            ->toArray();

        if (!in_array($section, $teacherSections)) {
            return response()->json(['error' => 'Access denied to this section'], 403);
        }

        // Get students from the section with their progress data
        $students = DB::table('student_profile')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->where('student_profile.section', $section)
            ->where('users.status', 'active')
            ->select([
                'student_profile.user_id',
                'student_profile.student_id',
                'student_profile.firstname',
                'student_profile.middlename',
                'student_profile.lastname',
                'student_profile.section',
                'student_profile.grade_level',
                'student_profile.school_year',
                'student_profile.total_points',
                'student_profile.current_streak',
                'student_profile.last_activity_date',
                'users.email'
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
                    'section' => $student->section,
                    'grade_level' => $student->grade_level,
                    'school_year' => $student->school_year,
                    'progress' => round($progress, 1),
                    'total_points' => $student->total_points,
                    'current_streak' => $student->current_streak,
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
     * Create a new section
     */
    public function store(Request $request)
    {
        $request->validate([
            'section' => 'required|string|max:50',
            'school_year' => 'nullable|string|max:20'
        ]);

        $teacher = Auth::guard('admin')->user();
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();

        if (!$teacherProfile) {
            return response()->json(['error' => 'Teacher profile not found'], 404);
        }

        // Check if section already exists for this teacher
        $existingSection = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->where('section', $request->section)
            ->where('school_year', $request->school_year)
            ->first();

        if ($existingSection) {
            return response()->json(['error' => 'Section already exists for this school year'], 422);
        }

        try {
            DB::table('teacher_sections')->insert([
                'teacher_id' => $teacherProfile->id,
                'section' => $request->section,
                'grade_level' => '6', // Fixed to Grade 6
                'school_year' => $request->school_year,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Section created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create section: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update a section
     */
    public function update(Request $request, $section)
    {
        $request->validate([
            'section' => 'required|string|max:50',
            'school_year' => 'nullable|string|max:20'
        ]);

        $teacher = Auth::guard('admin')->user();
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();

        if (!$teacherProfile) {
            return response()->json(['error' => 'Teacher profile not found'], 404);
        }

        try {
            DB::table('teacher_sections')
                ->where('teacher_id', $teacherProfile->id)
                ->where('section', $section)
                ->update([
                    'section' => $request->section,
                    'grade_level' => '6', // Fixed to Grade 6
                    'school_year' => $request->school_year,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Section updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update section: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a section
     */
    public function destroy($section)
    {
        $teacher = Auth::guard('admin')->user();
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();

        if (!$teacherProfile) {
            return response()->json(['error' => 'Teacher profile not found'], 404);
        }

        try {
            // Check if there are students in this section
            $studentCount = DB::table('student_profile')
                ->where('section', $section)
                ->count();

            if ($studentCount > 0) {
                return response()->json([
                    'error' => 'Cannot delete section with existing students. Please move students to another section first.'
                ], 422);
            }

            DB::table('teacher_sections')
                ->where('teacher_id', $teacherProfile->id)
                ->where('section', $section)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Section deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete section: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get average performance for a section
     */
    private function getSectionAveragePerformance($section)
    {
        // This is a simplified calculation - you can enhance this based on your performance metrics
        $avgPoints = DB::table('student_profile')
            ->where('section', $section)
            ->avg('total_points');
            
        return $avgPoints ? round($avgPoints, 1) : 0;
    }

    /**
     * Get last activity for a section
     */
    private function getLastActivityForSection($section)
    {
        $lastActivity = DB::table('student_profile')
            ->where('section', $section)
            ->whereNotNull('last_activity_date')
            ->max('last_activity_date');
            
        return $lastActivity ? \Carbon\Carbon::parse($lastActivity)->diffForHumans() : 'No activity';
    }
}
