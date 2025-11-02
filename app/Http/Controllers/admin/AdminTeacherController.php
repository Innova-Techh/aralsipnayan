<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\TeacherProfile;

class AdminTeacherController extends Controller
{
    /**
     * Display the teacher management page
     */
    public function index()
    {
        $teachers = DB::table('users')
            ->join('teacher_profile', 'users.id', '=', 'teacher_profile.user_id')
            ->leftJoin('teacher_sections', 'teacher_profile.id', '=', 'teacher_sections.teacher_id')
            ->where('users.role', 'Teacher')
            ->select(
                'users.id',
                'users.username',
                'users.email',
                'users.status',
                'users.created_at',
                'teacher_profile.firstname',
                'teacher_profile.lastname',
                'teacher_profile.school_name',
                'teacher_profile.profile_url',
                DB::raw('GROUP_CONCAT(DISTINCT teacher_sections.section SEPARATOR ", ") as sections'),
                DB::raw('COUNT(DISTINCT teacher_sections.id) as section_count')
            )
            ->groupBy(
                'users.id',
                'users.username',
                'users.email',
                'users.status',
                'users.created_at',
                'teacher_profile.firstname',
                'teacher_profile.lastname',
                'teacher_profile.school_name',
                'teacher_profile.profile_url'
            )
            ->orderBy('teacher_profile.lastname')
            ->get()
            ->map(function($teacher) {
                // Get student count for this teacher
                $studentCount = DB::table('teacher_sections')
                    ->join('teacher_profile', 'teacher_sections.teacher_id', '=', 'teacher_profile.id')
                    ->join('student_profile', 'teacher_sections.section', '=', 'student_profile.section')
                    ->where('teacher_profile.user_id', $teacher->id)
                    ->distinct('student_profile.user_id')
                    ->count('student_profile.user_id');
                
                $teacher->student_count = $studentCount;
                $teacher->sections = $teacher->sections ?? 'No sections assigned';
                
                return $teacher;
            });

        // Statistics
        $totalTeachers = $teachers->count();
        $activeTeachers = $teachers->where('status', 'active')->count();
        $totalSections = $teachers->sum('section_count');
        $totalStudents = $teachers->sum('student_count');

        return view('admin.admin.management.teacher-management', compact(
            'teachers',
            'totalTeachers',
            'activeTeachers',
            'totalSections',
            'totalStudents'
        ));
    }

    /**
     * Get teacher details
     */
    public function show($id)
    {       
            $teacher = DB::table('users')
                ->join('teacher_profile', 'users.id', '=', 'teacher_profile.user_id')
                ->where('users.id', $id)
                ->where('users.role', 'Teacher')
                ->select(
                    'users.id',
                    'users.username',
                    'users.email',
                    'users.status',
                    'teacher_profile.firstname',
                    'teacher_profile.lastname',
                    'teacher_profile.school_name',
                    'teacher_profile.profile_url'
                )
                ->first();


            // Get sections
            $sections = DB::table('teacher_sections')
                ->join('teacher_profile', 'teacher_sections.teacher_id', '=', 'teacher_profile.id')
                ->where('teacher_profile.user_id', $id)
                ->pluck('teacher_sections.section')
                ->toArray();

            $teacher->sections = $sections;

            return response()->json(['teacher' => $teacher]);
    }

    /**
     * Store a new teacher
     */
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'sections' => 'nullable|array',
            'sections.*' => 'string|max:50'
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt('123'),
                'role' => 'Teacher',
                'status' => 'active'
            ]);

            // Create teacher profile
            $teacherProfile = TeacherProfile::create([
                'user_id' => $user->id,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'school_name' => 'Pembo Elementary School',
                'profile_url' => '/profiles/default-teacher.png'
            ]);

            // Assign sections if provided
            if ($request->sections && count($request->sections) > 0) {
                $schoolYear = $request->school_year ?? '2024-2025';
                foreach ($request->sections as $section) {
                    // Ensure section exists in sections table for current school year
                    DB::table('sections')->updateOrInsert(
                        [
                            'name' => $section,
                            'school_year' => $schoolYear
                        ],
                        [
                            'grade_level' => '6',
                            'is_active' => true,
                            'updated_at' => now(),
                            'created_at' => now()
                        ]
                    );

                    // Assign section to teacher
                    DB::table('teacher_sections')->insert([
                        'teacher_id' => $teacherProfile->id,
                        'section' => $section,
                        'grade_level' => '6',
                        'school_year' => $schoolYear,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Teacher added successfully!',
                'teacher' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'firstname' => $teacherProfile->firstname,
                    'lastname' => $teacherProfile->lastname
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add teacher: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update teacher
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:3',
            'sections' => 'nullable|array',
            'sections.*' => 'string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // Update user
            $user = User::findOrFail($id);
            $user->username = $request->username;
            $user->email = $request->email;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();

            // Update teacher profile
            $teacherProfile = TeacherProfile::where('user_id', $id)->firstOrFail();
            $teacherProfile->firstname = $request->firstname;
            $teacherProfile->lastname = $request->lastname;
            $teacherProfile->save();

            // Handle sections update - ALWAYS update if sections key exists in request
            if ($request->has('sections')) {
                // Get the sections array (could be empty array)
                $newSections = $request->sections ?? [];
                
                // Remove all existing sections for this teacher
                DB::table('teacher_sections')
                    ->where('teacher_id', $teacherProfile->id)
                    ->delete();

                // Add new sections only if array is not empty
                if (!empty($newSections)) {
                    $schoolYear = $request->school_year ?? '2024-2025';
                    
                    // Remove duplicates from input
                    $newSections = array_unique(array_filter($newSections));
                    
                    foreach ($newSections as $section) {
                        // Skip empty values
                        if (empty(trim($section))) {
                            continue;
                        }

                        // Ensure section exists in sections table
                        DB::table('sections')->updateOrInsert(
                            [
                                'name' => trim($section),
                                'school_year' => $schoolYear
                            ],
                            [
                                'grade_level' => '6',
                                'is_active' => true,
                                'updated_at' => now(),
                                'created_at' => now()
                            ]
                        );

                        // Check if this assignment already exists (safety check)
                        $exists = DB::table('teacher_sections')
                            ->where('teacher_id', $teacherProfile->id)
                            ->where('section', trim($section))
                            ->where('school_year', $schoolYear)
                            ->exists();

                        // Only insert if it doesn't exist
                        if (!$exists) {
                            DB::table('teacher_sections')->insert([
                                'teacher_id' => $teacherProfile->id,
                                'section' => trim($section),
                                'grade_level' => '6',
                                'school_year' => $schoolYear,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Teacher updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Teacher update failed: ' . $e->getMessage(), [
                'teacher_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update teacher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive/Deactivate teacher
     */
    public function archive($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status = $user->status === 'active' ? 'inactive' : 'active';
            $user->save();

            $action = $user->status === 'inactive' ? 'archived' : 'activated';
            return response()->json([
                'success' => true,
                'message' => "Teacher {$action} successfully!",
                'status' => $user->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update teacher status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete teacher
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);
            $teacherProfile = TeacherProfile::where('user_id', $id)->first();

            if ($teacherProfile) {
                // Check if teacher has sections with students
                $hasStudents = DB::table('teacher_sections')
                    ->join('student_profile', 'teacher_sections.section', '=', 'student_profile.section')
                    ->where('teacher_sections.teacher_id', $teacherProfile->id)
                    ->exists();

                if ($hasStudents) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete teacher with assigned students. Please reassign students first.'
                    ], 422);
                }

                // Delete teacher sections
                DB::table('teacher_sections')
                    ->where('teacher_id', $teacherProfile->id)
                    ->delete();

                // Delete teacher profile
                $teacherProfile->delete();
            }

            // Delete user
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Teacher deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete teacher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available sections
     */
    public function getAvailableSections()
    {
        // Prefer sections table; fall back to student_profile unique sections if empty
        $sections = DB::table('sections')
            ->where('school_year', '2024-2025')
            ->orderBy('name')
            ->pluck('name');

        if ($sections->isEmpty()) {
            $sections = DB::table('student_profile')
                ->select('section')
                ->whereNotNull('section')
                ->distinct()
                ->orderBy('section')
                ->pluck('section');
        }

        return response()->json(['sections' => $sections]);
    }

    /**
     * Get sections for dropdown
     */
    public function getSections()
    {
        return $this->getAvailableSections();
    }

    /**
     * Toggle teacher status (archive/activate)
     */
    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status = $user->status === 'active' ? 'inactive' : 'active';
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Teacher status updated successfully!',
                'status' => $user->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update teacher status: ' . $e->getMessage()
            ], 500);
        }
    }
}