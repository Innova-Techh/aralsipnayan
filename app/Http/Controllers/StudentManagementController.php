<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
        
        // If no students in this section yet, create a basic section object
        if (!$sectionInfo) {
            $section = (object)[
                'id' => $sectionId,
                'name' => $sectionId,
                'grade_level' => null,
                'school_name' => null,
                'students_count' => $studentsCount,
                'active_students_count' => $activeStudentsCount,
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
            'contact_number' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:20',
            'address' => 'nullable|string',
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
                'contact_number' => null,
                'guardian_name' => null,
                'guardian_contact' => null,
                'address' => null,
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
            'status' => 'required|in:active,inactive,archived',
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
     * Archive a student
     */
    public function archive(Request $request, $studentId)
    {
        $user = User::findOrFail($studentId);

        if ($user->role !== 'Student') {
            return response()->json([
                'success' => false,
                'message' => 'User is not a student'
            ], 404);
        }

        $user->update([
            'status' => 'archived',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student archived successfully!'
        ]);
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
}