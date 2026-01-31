<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            ->orderByRaw("CASE WHEN users.status = 'active' THEN 1 ELSE 2 END ASC")
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
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email',
            'gender' => 'required|string|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        logger($request->all());
        DB::beginTransaction();
        try {
            // Check if user already exists by email
            $existingUser = User::where('email', $request->email)->first();
            
            if ($existingUser) {
                // User exists, check if they have a student profile
                $existingProfile = StudentProfile::where('user_id', $existingUser->id)->first();
                
                if ($existingProfile) {
                    // Student profile exists, check if already in this section
                    if ($existingProfile->section === $sectionId) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'This student is already enrolled in this section.'
                        ], 422);
                    }
                    
                    // Update existing profile to move to new section
                    $existingProfile->update([
                        'section' => $sectionId,
                        'firstname' => $request->firstname,
                        'middlename' => $request->middlename,
                        'lastname' => $request->lastname,
                    ]);
                    
                    $user = $existingUser;
                    $user->update(['status' => $request->status]);
                    $lrn = $existingProfile->student_id;
                } else {
                    // User exists but no student profile, create profile with new LRN
                    $lrn = $this->generateUniqueLRN();
                    
                    StudentProfile::create([
                        'user_id' => $existingUser->id,
                        'student_id' => $lrn,
                        'firstname' => $request->firstname,
                        'middlename' => $request->middlename,
                        'lastname' => $request->lastname,
                        'gender' => $request->gender,
                        'section' => $sectionId,
                        'grade_level' => $request->grade_level,
                        'school_name' => $this->getSchoolName($sectionId),
                        'has_completed_onboarding' => false,
                        'is_first_login' => true,
                    ]);
                    
                    $user = $existingUser;
                    $user->update([
                        'role' => 'Student',
                        'status' => $request->status
                    ]);
                }
            } else {
                // Completely new user, generate username and LRN
                $username = $this->generateSectionBasedUsername($sectionId);
                $lrn = $this->generateUniqueLRN();

                // Create user account
                $user = User::create([
                    'username' => $username,
                    'email' => $request->email,
                    'password' => Hash::make('123'), // Default password
                    'role' => 'Student',
                    'status' => 'Active',
                ]);

                // Create student profile
                StudentProfile::create([
                    'user_id' => $user->id,
                    'student_id' => $lrn,
                    'firstname' => $request->firstname,
                    'middlename' => $request->middlename,
                    'lastname' => $request->lastname,
                    'gender' => $request->gender,
                    'section' => $sectionId,
                    'grade_level' => '6',
                    'school_name' => $this->getSchoolName($sectionId),
                    'has_completed_onboarding' => false,
                    'is_first_login' => true,
                ]);
            }

            DB::commit();

            // Log notification
            $this->logNotification('student_management', 'Added new student to section', [
                'student_name' => trim($request->firstname . ' ' . ($request->middlename ? $request->middlename . ' ' : '') . $request->lastname),
                'student_id' => $lrn,
                'email' => $request->email,
                'section' => $sectionId,
                'student_user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student added successfully!',
                'student' => [
                    'user_id' => $user->id,
                    'student_id' => $lrn,
                    'name' => trim($request->firstname . ' ' . ($request->middlename ? $request->middlename . ' ' : '') . $request->lastname),
                    'email' => $request->email,
                    'section' => $sectionId,
                    'status' => $user->status
                ]
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
     * Get student data for editing (with auto-fill)
     */
    public function edit($studentId)
    {
        try {
            $user = User::with('studentProfile')->findOrFail($studentId);

            if ($user->role !== 'Student') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a student'
                ], 404);
            }

            $profile = $user->studentProfile;

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student profile not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'student' => [
                    'id' => $user->id,
                    'student_id' => $profile->student_id,
                    'firstname' => $profile->firstname,
                    'middlename' => $profile->middlename ?? '',
                    'lastname' => $profile->lastname,
                    'gender' => $profile->gender,
                    'email' => $user->email,
                    'section' => $profile->section,
                    'status' => $user->status,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch student data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update student information
     */
    public function update(Request $request, $studentId)
    {
        try {
            $user = User::with('studentProfile')->findOrFail($studentId);

            if ($user->role !== 'Student') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a student'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'student_id' => 'required|string|unique:student_profile,student_id,' . $user->studentProfile->id,
                'firstname' => 'required|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'lastname' => 'required|string|max:255',
                'password' => 'nullable|string|min:3',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'gender' => 'nullable|string|max:10',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            try {
                // Update user
                $user->update([
                    'email' => $request->email,
                    'status' => $request->status,
                ]);
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }

                $user->save();

                // Update student profile
                $user->studentProfile->update([
                    'student_id' => $request->student_id,
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'middlename' => $request->middlename,
                    'gender' => $request->gender,
                ]);

                DB::commit();

                // Log notification
                $this->logNotification('student_management', 'Updated student information', [
                    'student_name' => $request->firstname . ' ' . $request->lastname,
                    'student_id' => $request->student_id,
                    'email' => $request->email,
                    'student_user_id' => $studentId,
                    'password_changed' => $request->filled('password')
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Student updated successfully!',
                    'student' => $user->load('studentProfile')
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive/Activate a student
     * When archived: student status set to 'inactive' and they cannot log in and they are removed from their section
     */
    public function archive(Request $request, $studentId)
    {
        try {
            Log::info("Archive request received.", [
                'student_id' => $studentId,
                'requested_by' => auth()->user()->id ?? 'system'
            ]);
    
            $user = User::findOrFail($studentId);
    
            if ($user->role !== 'Student') {
                Log::warning("Archive aborted: User is not a student.", [
                    'user_id' => $user->id,
                    'role' => $user->role
                ]);
    
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a student.'
                ], 404);
            }
    
            Log::debug("Current status before toggle.", ['status' => $user->status]);
    
            // ✅ Toggle logic
            if ($user->status === 'active') {
                $newStatus = 'archive';
                $action = 'archive';
            } else {
                $newStatus = 'active';
                $action = 'activated';
            }
    
            Log::debug("New status determined.", [
                'old_status' => $user->status,
                'new_status' => $newStatus,
                'action' => $action
            ]);
            Log::debug('Updating user status.', [
                'user_id' => $user->id,
                'new_status' => $newStatus,
                'type' => gettype($newStatus)
            ]);
            // Update user status
            $user->update(['status' => $newStatus]);
            Log::info("User status updated.", [
                'user_id' => $user->id,
                'new_status' => $newStatus
            ]);
    
            // Remove section only when archiving (use empty string instead of null)
            if ($newStatus === 'archive') {
                DB::table('student_profile')
                    ->where('user_id', $studentId)
                    ->update(['section' => '']);
    
                Log::info("Student removed from section.", ['user_id' => $user->id]);
            }
            
            // Get student name for notification
            $studentProfile = $user->studentProfile;
            $studentName = $studentProfile ? $studentProfile->firstname . ' ' . $studentProfile->lastname : 'Unknown';
            
            // Log notification
            $this->logNotification('student_management', "Student {$action}", [
                'student_name' => $studentName,
                'student_id' => $studentProfile ? $studentProfile->student_id : 'N/A',
                'student_user_id' => $studentId,
                'action' => $action,
                'new_status' => $newStatus
            ]);
    
            Log::info("Archive toggle completed successfully.", [
                'user_id' => $user->id,
                'final_status' => $newStatus
            ]);
    
            return response()->json([
                'success' => true,
                'message' => "Student {$action} successfully!",
                'status' => $newStatus
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update student status.", [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
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
        try {
            $user = User::findOrFail($studentId);

            if ($user->role !== 'Student') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a student'
                ], 404);
            }

            DB::beginTransaction();
            try {
                // Store student info before deletion for notification
                $studentProfile = $user->studentProfile;
                $studentName = $studentProfile ? $studentProfile->firstname . ' ' . $studentProfile->lastname : 'Unknown';
                $studentId = $studentProfile ? $studentProfile->student_id : 'N/A';
                $section = $studentProfile ? $studentProfile->section : 'N/A';
                
                $user->studentProfile()->delete();
                $user->delete();

                DB::commit();

                // Log notification after successful deletion
                $this->logNotification('student_management', 'Deleted student', [
                    'student_name' => $studentName,
                    'student_id' => $studentId,
                    'section' => $section,
                    'deleted_user_id' => $user->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Student deleted successfully!'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore an archived student (reactivate)
     */
    public function restore($studentId)
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate section-based username (e.g., Section Einstein -> studenteinstein1, studenteinstein2, etc.)
     */
    private function generateSectionBasedUsername($section)
    {
        // Clean and normalize the section name (remove spaces and make lowercase)
        $sectionName = strtolower(preg_replace('/\s+/', '', trim($section)));
    
        // If section name is empty, default to "sectiona"
        if (empty($sectionName)) {
            $sectionName = 'sectiona';
        }
    
        $number = 1;
    
        while (true) {
            $username = 'student' . $sectionName . $number;
    
            // Check if this username already exists
            if (!User::where('username', $username)->exists()) {
                return $username;
            }
    
            $number++;
        }
    }

    /**
     * Generate unique LRN (Learner Reference Number) with LRN prefix
     * Format: LRN followed by 6 random digits
     */
    private function generateUniqueLRN()
    {
        do {
            // Generate a random 6-digit number (100000 to 999999)
            $number = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $lrn = 'LRN' . $number;
        } while (StudentProfile::where('student_id', $lrn)->exists());
        
        return $lrn;
    }

    /**
     * Get school name for the section
     */
    private function getSchoolName($sectionId)
    {
        $schoolName = DB::table('student_profile')
            ->where('section', $sectionId)
            ->value('school_name');
            
        return $schoolName ?? 'Default School';
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
     * Display all students management page
     */
    public function allStudents()
    {
        // Get all students regardless of section
        $students = User::where('role', 'Student')
            ->with('studentProfile')
            ->orderByRaw("CASE 
                WHEN users.status = 'active' THEN 1 
                WHEN users.status = 'inactive' THEN 2 
                ELSE 3 END ASC")
            ->orderBy('created_at', 'desc')
            ->get();

        // Get statistics
        $totalStudents = $students->count();
        $activeStudents = $students->where('status', 'active')->count();
        $archivedStudents = $students->where('status', 'archive')->count();
        
        // Get all unique sections
        $sections = DB::table('sections')
        ->join('teacher_sections', 'sections.name', '=', 'teacher_sections.section')
        ->where('sections.is_active', true)
        ->select('sections.name')
        ->distinct()
        ->pluck('sections.name');
        
        $totalSections = $sections->count();

        return view('admin.admin.management.all-students', compact(
            'students', 
            'totalStudents', 
            'activeStudents', 
            'archivedStudents',
            'sections',
            'totalSections'
        ));
    }

    /**
     * Store a new student (from all-students page)
     */
    public function storeAllStudents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required|string|max:10',
            'section' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Generate username based on section or default
            $section = $request->section ?? 'default';
            $username = $this->generateSectionBasedUsername($section);
            $lrn = $this->generateUniqueLRN();

            // Create user account
            $user = User::create([
                'username' => $username,
                'email' => $request->email,
                'password' => Hash::make('123'), // Default password
                'role' => 'Student',
                'status' => 'active',
            ]);

            // Get school name if section is provided
            $schoolName = $request->section ? $this->getSchoolName($request->section) : 'Default School';

            // Create student profile
            StudentProfile::create([
                'user_id' => $user->id,
                'student_id' => $lrn,
                'firstname' => $request->firstname,
                'middlename' => $request->middlename,
                'lastname' => $request->lastname,
                'gender' => $request->gender,
                'section' => $request->section,
                'grade_level' => '6',
                'school_name' => $schoolName,
                'has_completed_onboarding' => false,
                'is_first_login' => true,
            ]);

            DB::commit();

            // Log notification
            $this->logNotification('student_management', 'Added new student', [
                'student_name' => trim($request->firstname . ' ' . ($request->middlename ? $request->middlename . ' ' : '') . $request->lastname),
                'student_id' => $lrn,
                'email' => $request->email,
                'section' => $request->section ?? 'Unassigned',
                'student_user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student added successfully!',
                'student' => [
                    'user_id' => $user->id,
                    'student_id' => $lrn,
                    'name' => trim($request->firstname . ' ' . ($request->middlename ? $request->middlename . ' ' : '') . $request->lastname),
                    'email' => $request->email,
                    'section' => $request->section,
                    'status' => $user->status
                ]
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
     * Get student data for editing (from all-students page)
     */
    public function editAllStudents($studentId)
    {
        try {
            $user = User::with('studentProfile')->findOrFail($studentId);

            if ($user->role !== 'Student') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a student'
                ], 404);
            }

            $profile = $user->studentProfile;

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student profile not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'student' => [
                    'id' => $user->id,
                    'student_id' => $profile->student_id,
                    'firstname' => $profile->firstname,
                    'middlename' => $profile->middlename ?? '',
                    'lastname' => $profile->lastname,
                    'gender' => $profile->gender,
                    'email' => $user->email,
                    'section' => $profile->section,
                    'status' => $user->status,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch student data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update student information (from all-students page)
     */
    public function updateAllStudents(Request $request, $studentId)
    {
        try {
            $user = User::with('studentProfile')->findOrFail($studentId);

            if ($user->role !== 'Student') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a student'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'student_id' => 'required|string|unique:student_profile,student_id,' . $user->studentProfile->id,
                'firstname' => 'required|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'gender' => 'nullable|string|max:10',
                'password' => 'nullable|string|min:3',
                'section' => 'nullable|string',
                'status' => 'required|in:active,inactive,archive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            try {
                // Update user
                $user->update([
                    'email' => $request->email,
                    'status' => $request->status,
                ]);
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }
                // If student is archived → remove from section (use empty string instead of null)
                if ($request->status === 'archive') {
                    DB::table('student_profile')
                        ->where('user_id', $studentId)
                        ->update(['section' => '']);

                    Log::info("Archived student unassigned from section.", [
                        'user_id' => $studentId
                    ]);
                }

                $user->save();

                // Update student profile
                $user->studentProfile->update([
                    'student_id' => $request->student_id,
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'middlename' => $request->middlename,
                    'gender' => $request->gender,
                    'section' => $request->section,
                ]);

                DB::commit();

                // Log notification
                $this->logNotification('student_management', 'Updated student information', [
                    'student_name' => $request->firstname . ' ' . $request->lastname,
                    'student_id' => $request->student_id,
                    'email' => $request->email,
                    'section' => $request->section ?? 'Unassigned',
                    'student_user_id' => $studentId,
                    'status' => $request->status,
                    'password_changed' => $request->filled('password')
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Student updated successfully!',
                    'student' => $user->load('studentProfile')
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student: ' . $e->getMessage()
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