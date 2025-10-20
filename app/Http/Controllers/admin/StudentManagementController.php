<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StudentManagementController extends Controller
{
    /**
     * Display the student management page for a specific section
     */
    public function index($sectionId)
    {
        // Get section information from student_profile - just get first record to know the section name
        $sectionInfo = DB::table('student_profile')
            ->where('section', $sectionId)
            ->select('section', 'grade_level', 'school_name')
            ->first();
        // Get students in this section
        $students = User::where('role', 'Student')
            ->with('studentProfile')
            ->whereHas('studentProfile', function($query) use ($sectionId) {
                $query->where('section', $sectionId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Count students
        $studentsCount = $students->count();
        $activeStudentsCount = $students->where('status', 'active')->count();
        
        // Get teacher for this section
        $teacher = $this->getSectionTeacher($sectionId);
        
        // If no students in this section yet, create a basic section object
        if (!$sectionInfo) {
            $section = (object)[
                'id' => $sectionId,
                'name' => $sectionId,
                'grade_level' => null,
                'school_name' => null,
                'students_count' => $studentsCount,
                'active_students_count' => $activeStudentsCount,
                'teacher' => $teacher,
            ];
            
            return view('admin.admin.management.student-management', compact('section', 'students'));
        }
        
        // Create section object for the view
        $section = (object)[
            'id' => $sectionId,
            'name' => $sectionInfo->section ?? $sectionId,
            'grade_level' => $sectionInfo->grade_level,
            'school_name' => $sectionInfo->school_name,
            'students_count' => $studentsCount,
            'active_students_count' => $activeStudentsCount,
            'teacher' => $teacher,
        ];

        return view('admin.admin.management.student-management', compact('section', 'students'));
    }

    /**
     * Store a new student
     */
    public function store(Request $request, $sectionId)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|unique:student_profile,student_id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'grade_level' => 'required|integer|between:7,10',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Split full name
        $nameParts = explode(' ', $request->full_name, 3);
        $firstname = $nameParts[0] ?? '';
        $middlename = isset($nameParts[2]) ? $nameParts[1] : '';
        $lastname = isset($nameParts[2]) ? $nameParts[2] : ($nameParts[1] ?? '');

        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'username' => strtolower(str_replace(' ', '', $request->full_name)) . rand(100, 999),
                'email' => $request->email,
                'password' => Hash::make('password123'),
                'role' => 'Student',
                'status' => $request->status,
            ]);

            // Get school name from existing section data or use default
            $schoolName = DB::table('student_profile')
                ->where('section', $sectionId)
                ->value('school_name') ?? 'Default School';

            // Create student profile
            StudentProfile::create([
                'user_id' => $user->id,
                'student_id' => $request->student_id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'middlename' => $middlename,
                'section' => $sectionId,
                'grade_level' => $request->grade_level,
                'school_name' => $schoolName,
                'has_completed_onboarding' => false,
                'is_first_login' => true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student added successfully!',
                'student' => $user->load('studentProfile')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student data for editing
     */
    public function edit($studentId)
    {
        $user = User::with('studentProfile')->findOrFail($studentId);

        if ($user->role !== 'Student') {
            return response()->json([
                'success' => false,
                'message' => 'User is not a student'
            ], 404);
        }

        $profile = $user->studentProfile;

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $user->id,
                'student_id' => $profile->student_id ?? '',
                'name' => $profile ? $profile->firstname . ' ' . ($profile->middlename ? $profile->middlename . ' ' : '') . $profile->lastname : '',
                'email' => $user->email,
                'grade_level' => $profile->grade_level ?? '',
                'date_of_birth' => null,
                'gender' => null,
                'status' => $user->status,
            ]
        ]);
    }

    /**
     * Update student information
     */
    public function update(Request $request, $studentId)
    {
        $user = User::with('studentProfile')->findOrFail($studentId);

        if ($user->role !== 'Student') {
            return response()->json([
                'success' => false,
                'message' => 'User is not a student'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|unique:student_profile,student_id,' . $user->studentProfile->id,
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'grade_level' => 'required|integer|between:7,10',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Split full name
        $nameParts = explode(' ', $request->full_name, 3);
        $firstname = $nameParts[0] ?? '';
        $middlename = isset($nameParts[2]) ? $nameParts[1] : '';
        $lastname = isset($nameParts[2]) ? $nameParts[2] : ($nameParts[1] ?? '');

        DB::beginTransaction();
        try {
            // Update user
            $user->update([
                'email' => $request->email,
                'status' => $request->status,
            ]);

            // Update student profile
            $user->studentProfile->update([
                'student_id' => $request->student_id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'middlename' => $middlename,
                'grade_level' => $request->grade_level,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully!',
                'student' => $user->load('studentProfile')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive/Activate a student
     */
    public function archive(Request $request, $studentId)
    {
        try {
            $user = User::findOrFail($studentId);

            if ($user->role !== 'Student') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a student'
                ], 404);
            }

            // Toggle status: active -> inactive, inactive -> active
            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            $action = $newStatus === 'active' ? 'activated' : 'archived';

            $user->update([
                'status' => $newStatus,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Student {$action} successfully!",
                'status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete a student
     */
    public function destroy($studentId)
    {
        $user = User::findOrFail($studentId);

        if ($user->role !== 'Student') {
            return response()->json([
                'success' => false,
                'message' => 'User is not a student'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $user->studentProfile()->delete();
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore an archived student
     */
    public function restore($studentId)
    {
        $user = User::findOrFail($studentId);

        if ($user->role !== 'Student') {
            return response()->json([
                'success' => false,
                'message' => 'User is not a student'
            ], 404);
        }

        $user->update([
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student restored successfully!'
        ]);
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
}