<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    /**
     * Show assessment management page
     */
    public function index()
    {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return redirect()->route('admin.login');
        }

        // Get current teacher's sections
        $teacherId = Auth::guard('admin')->user()->id;
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacherId)->first();
        
        if (!$teacherProfile) {
            // If no teacher profile, return empty data
            $teacherSections = [];
            $studentsData = [];
        } else {
            $teacherSections = DB::table('teacher_sections')
                ->where('teacher_id', $teacherProfile->id)
                ->pluck('section')
                ->toArray();

            // Get students from teacher's sections
            if (!empty($teacherSections)) {
                $studentsData = DB::table('student_profile')
                    ->join('users', 'student_profile.user_id', '=', 'users.id')
                    ->whereIn('student_profile.section', $teacherSections)
                    ->where('users.status', 'active')
                    ->select([
                        'student_profile.user_id',
                        'student_profile.firstname',
                        'student_profile.lastname',
                        'student_profile.section',
                        'student_profile.grade_level'
                    ])
                    ->get()
                    ->toArray();
            } else {
                $studentsData = [];
            }
        }

        // Debug logging
        \Log::info('AssessmentController index - Teacher sections:', ['sections' => $teacherSections]);
        \Log::info('AssessmentController index - Students count:', ['count' => count($studentsData)]);

        return view('admin.teacher.assessments.index', [
            'teacherSections' => $teacherSections,
            'studentsData' => $studentsData
        ]);
    }

    /**
     * Show create assessment page
     */
    public function create()
    {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return redirect()->route('admin.login');
        }

        return view('admin.teacher.assessments.create');
    }

    /**
     * Get students for a specific section
     */
    public function getStudentsBySection(Request $request, $section)
    {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get current teacher's sections
        $teacherId = Auth::guard('admin')->user()->id;
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacherId)->first();
        $teacherSections = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->pluck('section')
            ->toArray();

        // Verify teacher has access to this section
        if (!in_array($section, $teacherSections)) {
            return response()->json([
                'error' => 'Access denied to this section',
                'debug' => [
                    'requested_section' => $section,
                    'teacher_sections' => $teacherSections,
                    'teacher_id' => $teacherId,
                    'teacher_profile_id' => $teacherProfile->id ?? 'not found'
                ]
            ], 403);
        }

        // Get students from the specified section
        $students = DB::table('student_profile')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->where('student_profile.section', $section)
            ->where('users.status', 'active')
            ->select([
                'student_profile.user_id',
                'student_profile.firstname',
                'student_profile.lastname',
                'student_profile.section',
                'student_profile.grade_level'
            ])
            ->get();

        return response()->json([
            'students' => $students,
            'section' => $section
        ]);
    }

    /**
     * Assign assessment to students
     */
    public function assignAssessment(Request $request)
    {
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'assessment_id' => 'required|string',
            'section' => 'required|string',
            'student_ids' => 'array',
            'accommodations' => 'boolean'
        ]);

        // Get current teacher's sections
        $teacherId = Auth::guard('admin')->user()->id;
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacherId)->first();
        $teacherSections = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->pluck('section')
            ->toArray();

        // Verify teacher has access to this section
        if (!in_array($request->section, $teacherSections)) {
            return response()->json(['error' => 'Access denied to this section'], 403);
        }

        // Here you would normally save the assignment to the database
        // For now, we'll just return a success response
        
        $assignmentData = [
            'assessment_id' => $request->assessment_id,
            'section' => $request->section,
            'student_ids' => $request->student_ids ?? [],
            'accommodations' => $request->accommodations ?? false,
            'assigned_by' => $teacherId,
            'assigned_at' => now()
        ];

        // Log the assignment (in a real implementation, you'd save this to a database)
        \Log::info('Assessment Assignment', $assignmentData);

        return response()->json([
            'success' => true,
            'message' => 'Assessment assigned successfully!',
            'assignment' => $assignmentData
        ]);
    }
}