<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminSectionController extends Controller
{
    /**
     * Display the section management page with dynamic data
     */
    public function index()
    {
        // Get all sections from sections table with their student counts and assigned teachers
        $sections = DB::table('sections')
            ->leftJoin('student_profile', 'sections.name', '=', 'student_profile.section')
            ->leftJoin('users', 'student_profile.user_id', '=', 'users.id')
            ->select(
                'sections.id',
                'sections.name as section',
                'sections.grade_level',
                'sections.school_year',
                'sections.is_active',
                DB::raw('COUNT(DISTINCT student_profile.user_id) as student_count'),
                DB::raw('MAX(student_profile.school_name) as school_name')
            )
            ->groupBy(
                'sections.id',
                'sections.name',
                'sections.grade_level', 
                'sections.school_year',
                'sections.is_active'
            )
            ->orderBy('sections.name')
            ->get();
    
        // Get statistics
        $totalSections = $sections->count();
        $totalStudents = $sections->sum('student_count');
        $activeSections = $sections->where('is_active', true)->count();
        $assignedTeachers = DB::table('teacher_sections')
            ->distinct('teacher_id')
            ->count();
    
       // Format sections with teachers - get teacher info separately to avoid duplicates
        $sectionsWithTeachers = $sections->map(function ($section) {
            // Get assigned teachers for this section
            $teachers = DB::table('teacher_sections')
                ->join('teacher_profile', 'teacher_sections.teacher_id', '=', 'teacher_profile.id')
                ->where('teacher_sections.section', $section->section)
                ->select('teacher_profile.firstname', 'teacher_profile.lastname')
                ->get();

            // Format teacher names
            $teacherNames = $teachers->map(function ($teacher) {
                return trim($teacher->firstname . ' ' . $teacher->lastname);
            })->toArray();

            return [
                'section' => $section->section,
                'grade_level' => $section->grade_level,
                'school_name' => $section->school_name ?? 'Not specified',
                'student_count' => $section->student_count,
                'teacher' => !empty($teacherNames) ? implode(', ', $teacherNames) : 'Unassigned',
                'status' => $section->is_active ? 'Active' : 'Inactive',
                'is_active' => (bool) $section->is_active // ✅ add this line
            ];
        });
    
        return view('admin.admin.management.section-management', compact(
            'sectionsWithTeachers',
            'totalSections',
            'totalStudents',
            'activeSections',
            'assignedTeachers'
        ));
    }

    /**
     * Get teacher assigned to a section
     */
    private function getSectionTeacher($section)
    {
        // Try to get teacher from teacher_sections table first
        $teacherSection = DB::table('teacher_sections')
            ->join('teacher_profile', 'teacher_sections.teacher_id', '=', 'teacher_profile.id')
            ->join('users', 'teacher_profile.user_id', '=', 'users.id')
            ->where('teacher_sections.section', $section)
            ->select('teacher_profile.firstname', 'teacher_profile.lastname')
            ->first();

        if ($teacherSection) {
            return $teacherSection->firstname . ' ' . $teacherSection->lastname;
        }

        // If no teacher found in teacher_sections, try to get from student_profile
        $studentTeacher = DB::table('student_profile')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->where('student_profile.section', $section)
            ->where('users.role', 'Teacher')
            ->select('student_profile.firstname', 'student_profile.lastname')
            ->first();

        if ($studentTeacher) {
            return $studentTeacher->firstname . ' ' . $studentTeacher->lastname;
        }

        // Default teacher names based on section
        $defaultTeachers = [
            'Einstein' => 'Ms. Maria Santos',
            'Newton' => 'Mr. John Cruz', 
            'Curie' => 'Ms. Ana Reyes'
        ];

        return $defaultTeachers[$section] ?? 'Unassigned';
    }

    /**
     * Get count of assigned teachers
     */
    private function getAssignedTeachersCount()
    {
        $uniqueTeachers = DB::table('teacher_sections')
            ->join('teacher_profile', 'teacher_sections.teacher_id', '=', 'teacher_profile.id')
            ->distinct('teacher_profile.id')
            ->count();

        return $uniqueTeachers > 0 ? $uniqueTeachers : 3; // Default to 3 if no data
    }

    /**
     * Get students for a specific section
     */
    public function getSectionStudents($section)
    {
        // First verify the section exists in sections table
        $sectionExists = DB::table('sections')
            ->where('name', $section)
            ->where('is_active', true)
            ->exists();

        if (!$sectionExists) {
            return response()->json([
                'success' => false,
                'message' => 'Section not found or inactive'
            ], 404);
        }

        $students = DB::table('student_profile')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->where('student_profile.section', $section)
            ->select(
                'users.id',
                'users.email',
                'users.status',
                'student_profile.student_id',
                'student_profile.firstname',
                'student_profile.lastname',
                'student_profile.middlename',
                'student_profile.grade_level'
            )
            ->orderBy('student_profile.lastname')
            ->get();

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    /**
     * Get available teachers for section assignment
     */
    public function getAvailableTeachers()
    {
        $teachers = DB::table('teacher_profile')
            ->join('users', 'teacher_profile.user_id', '=', 'users.id')
            ->where('users.status', 'active')
            ->where('users.role', 'Teacher')
            ->select(
                'teacher_profile.id',
                'teacher_profile.firstname',
                'teacher_profile.lastname',
                'teacher_profile.school_name'
            )
            ->orderBy('teacher_profile.lastname')
            ->get();

        return response()->json([
            'success' => true,
            'teachers' => $teachers
        ]);
    }

    /**
     * Create a new section (without teacher assignment)
     */
    public function storeSection(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'grade_level' => 'required|string|max:10',
            'school_year' => 'required|string|max:20'
        ]);

        try {
            // Check if section already exists
            $exists = DB::table('sections')
                ->where('name', $request->name)
                ->where('school_year', $request->school_year)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Section already exists for this school year.'
                ], 422);
            }

            // Insert into sections table
            DB::table('sections')->insert([
                'name' => $request->name,
                'grade_level' => $request->grade_level,
                'school_year' => $request->school_year,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Log notification
            $this->logNotification('section_management', 'Created new section', [
                'section_name' => $request->name,
                'grade_level' => $request->grade_level,
                'school_year' => $request->school_year
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Section created successfully!',
                'section' => $request->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create section: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign a teacher to a section (your original store method - renamed)
     */
    public function assignTeacherToSection(Request $request)
    {
        $request->validate([
            'section_name' => 'required|string|max:50|exists:sections,name',
            'assigned_teacher' => 'required|integer|exists:teacher_profile,id'
        ]);

        try {
            DB::beginTransaction();

            $schoolYear = '2024-2025';

            // Check if this teacher is already assigned to this section
            $existingAssignment = DB::table('teacher_sections')
                ->where('teacher_id', $request->assigned_teacher)
                ->where('section', $request->section_name)
                ->where('school_year', $schoolYear)
                ->first();

            if ($existingAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'This teacher is already assigned to this section!'
                ], 400);
            }

            // Create new section assignment
            DB::table('teacher_sections')->insert([
                'teacher_id' => $request->assigned_teacher,
                'section' => $request->section_name,
                'grade_level' => '6',
                'school_year' => $schoolYear,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            // Get teacher name for notification
            $teacher = DB::table('teacher_profile')->where('id', $request->assigned_teacher)->first();
            $teacherName = $teacher ? $teacher->firstname . ' ' . $teacher->lastname : 'Unknown';

            // Log notification
            $this->logNotification('section_management', 'Assigned teacher to section', [
                'section_name' => $request->section_name,
                'teacher_name' => $teacherName,
                'teacher_id' => $request->assigned_teacher,
                'school_year' => $schoolYear
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Teacher assigned to section successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign teacher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create section AND assign teacher (combined - keeps your original functionality)
     */
    public function store(Request $request)
    {
        $request->validate([
            'section_name' => 'required|string|max:50',
            'assigned_teacher' => 'required|integer|exists:teacher_profile,id'
        ]);

        try {
            DB::beginTransaction();

            $schoolYear = '2024-2025';
            $gradeLevel = '6';

            // 1. Create section in sections table if it doesn't exist
            $sectionExists = DB::table('sections')
                ->where('name', $request->section_name)
                ->where('school_year', $schoolYear)
                ->exists();

            if (!$sectionExists) {
                DB::table('sections')->insert([
                    'name' => $request->section_name,
                    'grade_level' => $gradeLevel,
                    'school_year' => $schoolYear,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // 2. Check if teacher is already assigned to this section
            $existingAssignment = DB::table('teacher_sections')
                ->where('teacher_id', $request->assigned_teacher)
                ->where('section', $request->section_name)
                ->where('school_year', $schoolYear)
                ->first();

            if ($existingAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'This teacher is already assigned to this section!'
                ], 400);
            }

            // 3. Assign teacher to section
            DB::table('teacher_sections')->insert([
                'teacher_id' => $request->assigned_teacher,
                'section' => $request->section_name,
                'grade_level' => $gradeLevel,
                'school_year' => $schoolYear,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            // Get teacher name for notification
            $teacher = DB::table('teacher_profile')->where('id', $request->assigned_teacher)->first();
            $teacherName = $teacher ? $teacher->firstname . ' ' . $teacher->lastname : 'Unknown';

            // Log notification
            $this->logNotification('section_management', 'Created section and assigned teacher', [
                'section_name' => $request->section_name,
                'teacher_name' => $teacherName,
                'teacher_id' => $request->assigned_teacher,
                'grade_level' => $gradeLevel,
                'school_year' => $schoolYear,
                'new_section' => !$sectionExists
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Section created and teacher assigned successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create section: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a section
     */
    public function update(Request $request, $section)
    {
        $request->validate([
            'section_name' => 'required|string|max:100',
            'grade_level' => 'required|integer|between:1,12',
            'enrolled_students' => 'required|integer|min:0|max:100',
            'assigned_teacher' => 'required|string|max:100'
        ]);

        try {
            // Update section data in student_profile table
            DB::table('student_profile')
                ->where('section', $section)
                ->update([
                    'section' => $request->section_name,
                    'grade_level' => $request->grade_level,
                    'updated_at' => now()
                ]);

            // Log notification
            $this->logNotification('section_management', 'Updated section', [
                'old_section_name' => $section,
                'new_section_name' => $request->section_name,
                'grade_level' => $request->grade_level,
                'enrolled_students' => $request->enrolled_students,
                'assigned_teacher' => $request->assigned_teacher
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Section updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update section: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Archive a section (set is_active = false)
     */
    public function archive(Request $request, $section)
    {
        try {
            // Check if section exists
            $sectionRecord = DB::table('sections')
                ->where('name', $section)
                ->first();

            if (!$sectionRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Section not found.'
                ], 404);
            }

            // Archive (deactivate) the section
            DB::table('sections')
                ->where('name', $section)
                ->update([
                    'is_active' => false,
                    'updated_at' => now(),
                ]);

            // Log notification
            $this->logNotification('section_management', 'Archived section', [
                'section_name' => $section,
                'grade_level' => $sectionRecord->grade_level,
                'school_year' => $sectionRecord->school_year
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Section archived successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive section: ' . $e->getMessage(),
            ], 500);
        }
    }
        /**
     * Reactivate (unarchive) a section
     */
    public function activate(Request $request, $section)
    {
        try {
            // Check if section exists
            $sectionRecord = DB::table('sections')
                ->where('name', $section)
                ->first();

            if (!$sectionRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Section not found.'
                ], 404);
            }

            // Activate the section
            DB::table('sections')
                ->where('name', $section)
                ->update([
                    'is_active' => true,
                    'updated_at' => now(),
                ]);

            // Log notification
            $this->logNotification('section_management', 'Reactivated section', [
                'section_name' => $section,
                'grade_level' => $sectionRecord->grade_level,
                'school_year' => $sectionRecord->school_year
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Section reactivated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reactivate section: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a section permanently
     */
    public function destroy($section)
    {
        try {
            DB::beginTransaction();

            // Get section info before deletion
            $sectionRecord = DB::table('sections')->where('name', $section)->first();
            $studentCount = DB::table('student_profile')->where('section', $section)->count();

            // Delete student profiles first
            $studentIds = DB::table('student_profile')
                ->where('section', $section)
                ->pluck('user_id');

            DB::table('student_profile')
                ->where('section', $section)
                ->delete();

            // Delete users
            DB::table('users')
                ->whereIn('id', $studentIds)
                ->delete();

            DB::commit();

            // Log notification after successful deletion
            $this->logNotification('section_management', 'Deleted section', [
                'section_name' => $section,
                'grade_level' => $sectionRecord ? $sectionRecord->grade_level : 'Unknown',
                'school_year' => $sectionRecord ? $sectionRecord->school_year : 'Unknown',
                'students_removed' => $studentCount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Section deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete section: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log notification to admin_notifications table
     */
    private function logNotification($type, $action, $details = [])
    {
        try {
            $admin = Auth::guard('admin')->user();
            $adminProfile = $admin ? $admin->adminProfile : null;
            
            if ($adminProfile) {
                DB::table('admin_notifications')->insert([
                    'admin_id' => $adminProfile->id,
                    'type' => $type,
                    'action' => $action,
                    'details' => json_encode(array_merge($details, [
                        'timestamp' => now()->toDateTimeString(),
                        'admin_username' => $admin->username
                    ])),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log admin notification: ' . $e->getMessage());
        }
    }
}
