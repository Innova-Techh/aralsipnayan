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
                'student_profile.firstname',
                'student_profile.lastname',
                'student_profile.section',
                'student_profile.grade_level',
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
                    'name' => $student->firstname . ' ' . $student->lastname,
                    'email' => $student->email,
                    'section' => $student->section,
                    'grade_level' => $student->grade_level,
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
     * Store a new student
     */
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email',
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
                        'school_year' => $request->school_year,
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
                        'section' => $request->section,
                        'grade_level' => '6', // Fixed to Grade 6
                        'school_year' => $request->school_year,
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
                    'grade_level' => '6', // Fixed to Grade 6
                    'school_year' => $request->school_year,
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
            'email' => 'required|email|unique:users,email,' . $studentId,
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

            // Update student profile
            $studentProfile = StudentProfile::where('user_id', $studentId)->firstOrFail();
            $studentProfile->update([
                'firstname' => $request->firstname,
                'middlename' => $request->middlename,
                'lastname' => $request->lastname,
                'section' => $request->section,
                'grade_level' => '6', // Fixed to Grade 6
                'school_year' => $request->school_year
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

    /**
     * Generate section-based username (e.g., Section A -> studenta1, studenta2, etc.)
     */
    private function generateSectionBasedUsername($section)
    {
        // Extract the section letter (A, B, C, etc.) from section name
        $sectionLetter = strtolower(substr(trim($section), -1)); // Get last character and make lowercase
        
        // If section doesn't end with a letter, default to 'a'
        if (!ctype_alpha($sectionLetter)) {
            $sectionLetter = 'a';
        }
        
        $number = 1;

        while (true) {
            $username = 'student' . $sectionLetter . $number;
            
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
