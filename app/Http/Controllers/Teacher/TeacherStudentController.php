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
use Illuminate\Support\Facades\Hash;

class TeacherStudentController extends Controller
{
    /**
     * Display the student management page with sections overview
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

                // Get average performance for this section (if we have performance data)
                $averagePerformance = $this->getSectionAveragePerformance($section);

                $sectionsData[] = [
                    'section' => $section,
                    'student_count' => $studentCount,
                    'active_assessments' => $activeAssessmentsCount,
                    'average_performance' => $averagePerformance,
                    'last_activity' => $this->getLastActivityForSection($section)
                ];
            }
        }

        return view('admin.teacher.students.index', compact('sectionsData'));
    }


    /**
     * Store a new student
     */
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email',
            'gender' => 'required|string|max:10',
            'section' => 'required|string',
            'school_year' => 'nullable|string|max:20'
        ]);

        // Check for email uniqueness only for truly new users
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            $existingProfile = StudentProfile::where('user_id', $existingUser->id)->first();
            if (!$existingProfile) {
                // User exists but no student profile - this is okay, we'll create the profile
            } else {
                // Check if this existing student is already in a different teacher's section
                $teacherProfile = DB::table('teacher_profile')->where('user_id', Auth::guard('admin')->user()->id)->first();
                $teacherSections = DB::table('teacher_sections')
                    ->where('teacher_id', $teacherProfile->id)
                    ->pluck('section')
                    ->toArray();
                
                if (!in_array($existingProfile->section, $teacherSections) && $existingProfile->section !== $request->section) {
                    return response()->json(['error' => 'This student already exists in another section that you do not manage.'], 422);
                }
            }
        }

        $teacher = Auth::guard('admin')->user();
        
        // Verify teacher has access to this section
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        $teacherSections = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->pluck('section')
            ->toArray();

        if (!in_array($request->section, $teacherSections)) {
            return response()->json(['error' => 'Access denied to this section'], 403);
        }

        DB::beginTransaction();
        try {
            // Check if user already exists by email
            $existingUser = User::where('email', $request->email)->first();
            
            if ($existingUser) {
                // User exists, check if they have a student profile
                $existingProfile = StudentProfile::where('user_id', $existingUser->id)->first();
                
                if ($existingProfile) {
                    // Student profile exists, just update the section and school year
                    $existingProfile->update([
                        'section' => $request->section,
                        'firstname' => $request->firstname,
                        'middlename' => $request->middlename,
                        'lastname' => $request->lastname,
                    ]);
                    
                    $user = $existingUser;
                    $lrn = $existingProfile->student_id; // Use existing LRN
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
                        'section' => $request->section,
                        'grade_level' => '6', // Fixed to Grade 6
                        'school_year' => $request->school_year ?: '2024-2025',
                        'total_points' => 0,
                        'current_streak' => 0,
                        'longest_streak' => 0
                    ]);
                    
                    $user = $existingUser;
                }
            } else {
                // Completely new user, generate username and LRN
                $username = $this->generateSectionBasedUsername($request->section);
                $lrn = $this->generateUniqueLRN();

                // Create user account
                $user = User::create([
                    'username' => $username,
                    'email' => $request->email,
                    'password' => bcrypt('123'), // Default password
                    'role' => 'Student',
                    'status' => 'active'
                ]);

                // Create student profile
                StudentProfile::create([
                    'user_id' => $user->id,
                    'student_id' => $lrn,
                    'firstname' => $request->firstname,
                    'middlename' => $request->middlename,
                    'lastname' => $request->lastname,
                    'section' => $request->section,
                    'gender' => $request->gender,
                    'grade_level' => '6', // Fixed to Grade 6
                    'school_year' => '2024-2025',
                    'total_points' => 0,
                    'current_streak' => 0,
                    'longest_streak' => 0
                ]);
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Student processed successfully!',
                'student' => [
                    'user_id' => $user->id,
                    'student_id' => $lrn,
                    'name' => trim($request->firstname . ' ' . ($request->middlename ? $request->middlename . ' ' : '') . $request->lastname),
                    'email' => $request->email,
                    'section' => $request->section
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to create student: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update a student
     */
    public function update(Request $request, $studentId)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'password' => 'nullable|string|min:3',
            'email' => 'required|email|unique:users,email,' . $studentId,
            'gender' => 'nullable|string|max:10',
            'section' => 'required|string',
            'school_year' => 'nullable|string|max:20'
        ]);

        $teacher = Auth::guard('admin')->user();
        
        // Verify teacher has access to this section
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        $teacherSections = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->pluck('section')
            ->toArray();

        if (!in_array($request->section, $teacherSections)) {
            return response()->json(['error' => 'Access denied to this section'], 403);
        }

        DB::beginTransaction();
        try {
            // Update user
            $user = User::findOrFail($studentId);
            $user->update(['email' => $request->email]);
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            // Update student profile
            $studentProfile = StudentProfile::where('user_id', $studentId)->firstOrFail();
            $studentProfile->update([
                'firstname' => $request->firstname,
                'middlename' => $request->middlename,
                'lastname' => $request->lastname,
                'section' => $request->section,
                'gender' => $request->gender,
                'grade_level' => '6', // Fixed to Grade 6
                'school_year' => $request->school_year ?: '2024-2025'
            ]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to update student: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a student
     */
    public function destroy($studentId)
    {
        $teacher = Auth::guard('admin')->user();
        
        // Verify the student belongs to teacher's section
        $studentProfile = StudentProfile::where('user_id', $studentId)->first();
        if (!$studentProfile) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        $teacherSections = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->pluck('section')
            ->toArray();

        if (!in_array($studentProfile->section, $teacherSections)) {
            return response()->json(['error' => 'Access denied to this student'], 403);
        }

        DB::beginTransaction();
        try {
            // Delete student profile first
            $studentProfile->delete();
            
            // Delete user account completely
            $user = User::findOrFail($studentId);
            $user->delete();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to delete student: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Deactivate a student's account (set users.status to 'inactive').
     */
    public function deactivate($studentId)
    {
        $teacher = Auth::guard('admin')->user();

        // Verify the student exists
        $studentProfile = StudentProfile::where('user_id', $studentId)->first();
        if (!$studentProfile) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // Verify teacher manages this student's section
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        $teacherSections = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->pluck('section')
            ->toArray();

        if (!in_array($studentProfile->section, $teacherSections)) {
            return response()->json(['error' => 'Access denied to this student'], 403);
        }

        try {
            $user = User::findOrFail($studentId);
            $user->status = 'inactive';
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Student account set to inactive.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to deactivate student: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Activate a student's account (set users.status to 'active').
     */
    public function activate($studentId)
    {
        $teacher = Auth::guard('admin')->user();

        // Verify the student exists
        $studentProfile = StudentProfile::where('user_id', $studentId)->first();
        if (!$studentProfile) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // Verify teacher manages this student's section
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        $teacherSections = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->pluck('section')
            ->toArray();

        if (!in_array($studentProfile->section, $teacherSections)) {
            return response()->json(['error' => 'Access denied to this student'], 403);
        }

        try {
            $user = User::findOrFail($studentId);
            $user->status = 'active';
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Student account set to active.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to activate student: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove a student from a section.
     */
    public function removeFromSection(Request $request, $studentId)
    {
        $teacher = Auth::guard('admin')->user();

        // Validate student existence
        $studentProfile = DB::table('student_profile')->where('user_id', $studentId)->first();
        if (!$studentProfile) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // Validate teacher access
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        $teacherSections = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->pluck('section')
            ->toArray();

        if (!in_array($studentProfile->section, $teacherSections)) {
            return response()->json(['error' => 'Access denied to this student'], 403);
        }

        try {
            // Remove the student from their section
            DB::table('student_profile')
                ->where('user_id', $studentId)
                ->update(['section' => null]); // Unassign section
            // ✅ Also mark them as inactive
            DB::table('users')
            ->where('id', $studentId)
            ->update(['status' => 'inactive']);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Student removed from section successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to remove student: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get average performance for a section from user_progress table
     */
    private function getSectionAveragePerformance($section)
    {
        
        try {
            Log::info('Active student IDs:', $students->toArray());
            // Get students in this section
            $students = DB::table('student_profile')
                ->join('users', 'student_profile.user_id', '=', 'users.id')
                ->where('student_profile.section', $section)
                ->where('users.status', 'active')
                ->pluck('student_profile.user_id');
            
            if ($students->isEmpty()) {
                return 0;
            } 

            // Calculate average score per student, then average those (Method 2)
            $studentAverages = [];
            foreach ($students as $studentId) {
                $avgScore = DB::table('assessments')
                    ->where('assessments.user_id', $studentId)
                    ->where('assessments.status', 'completed')
                    ->where('assessments.assessment_type', 'regular')
                    ->avg('assessments.accuracy_percentage');
                
                if ($avgScore) {
                    $studentAverages[] = $avgScore;
                }
            }
            
            $avgAccuracy = !empty($studentAverages) ? array_sum($studentAverages) / count($studentAverages) : 0;
            
            
            $result = $avgAccuracy ? round($avgAccuracy, 1) : 0;
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
     * Generate section-based username (e.g., Section A -> studenta1, studenta2, etc.)
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
     * Generate unique 6-digit LRN (Learner Reference Number) with LRN prefix
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
}