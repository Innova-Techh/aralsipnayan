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

class TeacherSectionController extends Controller
{
    /**
     * Show student profile with detailed information
     */
    public function showStudentProfile($studentId)
    {
        // Check authentication
        if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
            return redirect()->route('admin.login');
        }
        
        $teacher = Auth::guard('admin')->user();
        
        // Fetch the student data
        $user = User::find($studentId);
        
        if (!$user || $user->role !== 'Student') {
            return redirect()->back()->with('error', 'Student not found');
        }
        
        // Get student profile
        $studentProfile = DB::table('student_profile')
            ->where('user_id', $studentId)
            ->first();
        
        if (!$studentProfile) {
            return redirect()->back()->with('error', 'Student profile not found');
        }
        
        // Verify teacher has access to this student's section
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        
        if (!$teacherProfile) {
            return redirect()->back()->with('error', 'Teacher profile not found');
        }
        
        $teacherSections = DB::table('sections')
            ->join('teacher_sections', 'sections.name', '=', 'teacher_sections.section')
            ->where('teacher_sections.teacher_id', $teacherProfile->id)
            ->where('sections.is_active', true)
            ->pluck('sections.name')
            ->toArray();
        
        if (!in_array($studentProfile->section, $teacherSections)) {
            return redirect()->back()->with('error', 'Access denied to this student');
        }
        
        // Get section details
        $section = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfile->id)
            ->where('section', $studentProfile->section)
            ->first();
        
        // Get student's assessment history
        $assessmentResults = collect([]);
        
        try {
            if (DB::getSchemaBuilder()->hasTable('teacher_assessment_results')) {
                $assessmentResults = DB::table('teacher_assessment_results')
                    ->join('teacher_assessment_assignments', 'teacher_assessment_results.assignment_id', '=', 'teacher_assessment_assignments.id')
                    ->join('teacher_assessments', 'teacher_assessment_assignments.assessment_id', '=', 'teacher_assessments.id')
                    ->where('teacher_assessment_results.student_id', $studentId)
                    ->where('teacher_assessment_results.status', 'completed')
                    ->select([
                        'teacher_assessments.title as name',
                        'teacher_assessment_results.score',
                        'teacher_assessment_results.completed_at as date',
                        'teacher_assessment_results.time_spent as time',
                        'teacher_assessments.assessment_type as type'
                    ])
                    ->orderByDesc('teacher_assessment_results.completed_at')
                    ->limit(10)
                    ->get()
                    ->map(function($assessment) {
                        return [
                            'name' => $assessment->name,
                            'score' => round($assessment->score) . '%',
                            'date' => \Carbon\Carbon::parse($assessment->date)->format('Y-m-d'),
                            'time' => round($assessment->time / 60, 1) . ' min',
                            'type' => ucfirst($assessment->type)
                        ];
                    });
            }
        } catch (\Exception $e) {
            $assessmentResults = collect([]);
        }
        
        // Calculate statistics
        $totalAssessments = $assessmentResults->count();
        $avgScore = $assessmentResults->isNotEmpty() ? $assessmentResults->avg(function($item) {
            return (float) str_replace('%', '', $item['score']);
        }) : 0;
        
        // Build student object for the view
        $student = (object)[
            'id' => $user->id,
            'name' => trim($studentProfile->firstname . ' ' . ($studentProfile->middlename ? $studentProfile->middlename . ' ' : '') . $studentProfile->lastname),
            'email' => $user->email,
            'phone' => $studentProfile->phone ?? '+1 (555) 123-4567',
            'grade_level' => $studentProfile->grade_level ?? '6',
            'section_id' => $studentProfile->section,
            'total_points' => $studentProfile->total_points ?? 0,
            'assessment_count' => $totalAssessments,
            'best_streak' => $studentProfile->longest_streak ?? 0,
            'time_spent' => round(($studentProfile->total_points ?? 0) / 10, 1),
            'avg_time_per_question' => '1.8',
            'numerical_literacy_score' => 96,
            'algebraic_thinking_score' => 89,
            'geometric_reasoning_score' => 94,
            'problem_solving_score' => 91,
            'last_assessment_name' => $assessmentResults->first()['name'] ?? 'No assessments yet',
            'last_assessment_score' => $assessmentResults->first()['score'] ?? '0%',
            'last_assessment_date' => $assessmentResults->first()['date'] ?? 'N/A',
            'last_assessment_time' => $assessmentResults->first()['time'] ?? '0 min',
            'last_assessment_accuracy' => $assessmentResults->isNotEmpty() ? $assessmentResults->first()['score'] : '0%',
            'assessments' => $assessmentResults->toArray(),
            'struggling_areas' => [],
            'achievements' => []
        ];
        
        // Build section object
        $sectionObj = (object)[
            'id' => $section->id ?? null,
            'name' => $studentProfile->section
        ];
        
        // FIXED: Use the correct view path - teacher.sections.student-profile
        return view('admin.teacher.sections.student-profile', [
            'student' => $student,
            'section' => $sectionObj
        ]);
    }

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
                // Get student count for this section
                $studentCount = DB::table('student_profile')
                    ->join('users', 'student_profile.user_id', '=', 'users.id')
                    ->where('student_profile.section', $section->name)
                    ->where('users.status', 'active')
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

        // Get students with detailed information
        $students = DB::table('student_profile')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->where('student_profile.section', $section)
            // Include all users; status will be displayed in UI
            ->select([
                'student_profile.user_id',
                'student_profile.student_id',
                'student_profile.firstname',
                'student_profile.middlename',
                'student_profile.lastname',
                'student_profile.section',
                'student_profile.total_points',
                'student_profile.current_streak',
                'student_profile.last_activity_date',
                'users.email',
                'users.status'
            ])
            ->orderByDesc('student_profile.total_points')
            ->get()
            ->map(function ($student, $index) use ($section) {
                // Check if teacher_assessment_assignments table exists
                $totalAssessments = 0;
                $completedAssessments = 0;
                $avgScore = 0;

                try {
                    // Get total assignments for this section
                    $totalAssessments = DB::table('teacher_assessment_assignments')
                        ->where('section', $section)
                        ->count();

                    // Try to get completed assessments (if table exists)
                    if (DB::getSchemaBuilder()->hasTable('teacher_assessment_results')) {
                        $completedAssessments = DB::table('teacher_assessment_results')
                            ->join('teacher_assessment_assignments', 'teacher_assessment_results.assignment_id', '=', 'teacher_assessment_assignments.id')
                            ->where('teacher_assessment_results.student_id', $student->user_id)
                            ->where('teacher_assessment_assignments.section', $section)
                            ->where('teacher_assessment_results.status', 'completed')
                            ->count();

                        // Calculate average score
                        $avgScore = DB::table('teacher_assessment_results')
                            ->join('teacher_assessment_assignments', 'teacher_assessment_results.assignment_id', '=', 'teacher_assessment_assignments.id')
                            ->where('teacher_assessment_results.student_id', $student->user_id)
                            ->where('teacher_assessment_assignments.section', $section)
                            ->where('teacher_assessment_results.status', 'completed')
                            ->avg('teacher_assessment_results.score');
                    }
                } catch (\Exception $e) {
                    // If tables don't exist, use default values
                    $totalAssessments = 0;
                    $completedAssessments = 0;
                    $avgScore = 0;
                }

                // If no assessment data, use points-based score estimate
                if (!$avgScore) {
                    $avgScore = min(($student->total_points / 30), 100); // Estimate based on points
                }

                return [
                    'id' => $student->user_id,
                    'student_id' => $student->student_id,
                    'name' => trim($student->firstname . ' ' . ($student->middlename ? $student->middlename . ' ' : '') . $student->lastname),
                    'email' => $student->email,
                    'status' => $student->status,
                    'score' => round($avgScore),
                    'progress' => $totalAssessments > 0 ? "$completedAssessments/$totalAssessments" : "0/0",
                    'completed' => $completedAssessments,
                    'total' => $totalAssessments,
                    'points' => $student->total_points,
                    'streak' => $student->current_streak,
                    'last_activity' => $student->last_activity_date ? 
                        \Carbon\Carbon::parse($student->last_activity_date)->diffForHumans() : 
                        'Never',
                    'rank' => $index + 1
                ];
            });

        // Calculate section statistics
        $totalStudents = $students->count();
        $activeStudents = $students->count(); // All queried students are active
        $averageScore = $students->avg('score');
        
        // Calculate completion rate
        $totalPossibleCompletions = $students->sum('total');
        $totalCompletions = $students->sum('completed');
        $completionRate = $totalPossibleCompletions > 0 ? 
            round(($totalCompletions / $totalPossibleCompletions) * 100) : 0;

        // Get top performer
        $topPerformer = $students->first();
        $topPerformerName = $topPerformer ? $topPerformer['name'] : 'N/A';
        $topPerformerScore = $topPerformer ? $topPerformer['score'] : 0;

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
            'totalStudents' => $totalStudents,
            'activeStudents' => $activeStudents,
            'averageScore' => round($averageScore),
            'completionRate' => $completionRate,
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

        // Check if section already exists in sections table
        $existingSection = DB::table('sections')
            ->where('name', $request->section)
            ->where('school_year', $request->school_year)
            ->first();

        if ($existingSection) {
            return response()->json(['error' => 'Section already exists for this school year'], 422);
        }

        try {
            DB::beginTransaction();

            // First, create the section in sections table
            DB::table('sections')->insert([
                'name' => $request->section,
                'grade_level' => '6', // Fixed to Grade 6
                'school_year' => $request->school_year ?? '2024-2025',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Then, assign the teacher to the section
            DB::table('teacher_sections')->insert([
                'teacher_id' => $teacherProfile->id,
                'section' => $request->section,
                'grade_level' => '6', // Fixed to Grade 6
                'school_year' => $request->school_year ?? '2024-2025',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();


            return response()->json([
                'success' => true,
                'message' => 'Section created successfully!',
                'section' => $request->section // Include section name for frontend
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

            // Update the teacher_sections table
            DB::table('teacher_sections')
                ->where('teacher_id', $teacherProfile->id)
                ->where('section', $section)
                ->update([
                    'section' => $request->section,
                    'grade_level' => '6', // Fixed to Grade 6
                    'school_year' => $request->school_year,
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

            DB::beginTransaction();

            // Remove teacher assignment from teacher_sections
            DB::table('teacher_sections')
                ->where('teacher_id', $teacherProfile->id)
                ->where('section', $section)
                ->delete();

            // Check if any other teachers are assigned to this section
            $otherTeachers = DB::table('teacher_sections')
                ->where('section', $section)
                ->count();

            // If no other teachers are assigned, deactivate the section
            if ($otherTeachers == 0) {
                DB::table('sections')
                    ->where('name', $section)
                    ->update([
                        'is_active' => false,
                        'updated_at' => now()
                    ]);
            }

            DB::commit();

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
        try {
            // Get average accuracy from completed assessments for this section
            $avgAccuracy = DB::table('assessments')
                ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                ->where('student_profile.section', $section)
                ->where('assessments.status', 'completed')
                ->where('assessments.assessment_type', 'regular')
                ->avg('assessments.accuracy_percentage');
            
            // Debug: Log the performance calculation
            \Log::info("Section {$section} average performance calculation:", [
                'avg_accuracy' => $avgAccuracy,
                'section' => $section
            ]);
            
            return $avgAccuracy ? round($avgAccuracy, 1) : 0;
            
        } catch (\Exception $e) {
            
            // Fallback to total_points if assessment data is not available
            $avgPoints = DB::table('student_profile')
                ->where('section', $section)
                ->avg('total_points');
                
            return $avgPoints ? round($avgPoints, 1) : 0;
        }
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
 * Show detailed assessment review for a student
 */
public function reviewAssessment($studentId, $assessmentId)
{
    // Check authentication
    if (!Auth::guard('admin')->check() || Auth::guard('admin')->user()->role !== 'Teacher') {
        return redirect()->route('login');
    }
    
    $teacher = Auth::guard('admin')->user();
    
    // Fetch the student
    $user = User::find($studentId);
    
    if (!$user || $user->role !== 'Student') {
        return redirect()->back()->with('error', 'Student not found');
    }
    
    // Get student profile
    $studentProfile = DB::table('student_profile')
        ->where('user_id', $studentId)
        ->first();
    
    if (!$studentProfile) {
        return redirect()->back()->with('error', 'Student profile not found');
    }
    
    // Verify teacher has access to this student's section
    $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
    
    if (!$teacherProfile) {
        return redirect()->back()->with('error', 'Teacher profile not found');
    }
    
    $teacherSections = DB::table('sections')
        ->join('teacher_sections', 'sections.name', '=', 'teacher_sections.section')
        ->where('teacher_sections.teacher_id', $teacherProfile->id)
        ->where('sections.is_active', true)
        ->pluck('sections.name')
        ->toArray();
    
    if (!in_array($studentProfile->section, $teacherSections)) {
        return redirect()->back()->with('error', 'Access denied to this student');
    }
    
    // Build student object
    $student = (object)[
        'id' => $user->id,
        'name' => trim($studentProfile->firstname . ' ' . ($studentProfile->middlename ? $studentProfile->middlename . ' ' : '') . $studentProfile->lastname),
    ];
    
    // Try to fetch real assessment data
    $assessment = null;
    $result = null;
    $questions = [];
    
    try {
        // Check if this is a teacher-created assessment
        if (DB::getSchemaBuilder()->hasTable('teacher_assessments')) {
            $assessment = DB::table('teacher_assessments')
                ->where('id', $assessmentId)
                ->where('created_by', $teacher->id)
                ->first();
            
            if ($assessment) {
                // Get the assignment for this student
                $assignment = DB::table('teacher_assessment_assignments')
                    ->where('assessment_id', $assessmentId)
                    ->where('section', $studentProfile->section)
                    ->first();
                
                if ($assignment && DB::getSchemaBuilder()->hasTable('teacher_assessment_results')) {
                    // Get the result
                    $result = DB::table('teacher_assessment_results')
                        ->where('assignment_id', $assignment->id)
                        ->where('student_id', $studentId)
                        ->where('status', 'completed')
                        ->first();
                    
                    if ($result) {
                        // Get the questions and answers
                        if (DB::getSchemaBuilder()->hasTable('teacher_assessment_answers')) {
                            $answers = DB::table('teacher_assessment_answers')
                                ->where('result_id', $result->id)
                                ->get();
                            
                            // Get questions from the assessment
                            $assessmentQuestions = json_decode($assessment->questions, true);
                            
                            foreach ($assessmentQuestions as $index => $question) {
                                $answer = $answers->firstWhere('question_index', $index);
                                
                                $questions[] = [
                                    'is_correct' => $answer ? $answer->is_correct : false,
                                    'category' => $question['competency'] ?? 'General',
                                    'question_text' => $question['question'],
                                    'options' => [
                                        'A' => $question['options']['A'] ?? '',
                                        'B' => $question['options']['B'] ?? '',
                                        'C' => $question['options']['C'] ?? '',
                                        'D' => $question['options']['D'] ?? ''
                                    ],
                                    'student_answer' => $answer ? $answer->student_answer : null,
                                    'correct_answer' => $question['correct_answer'],
                                    'explanation' => $question['explanation'] ?? null,
                                    'time_spent' => $answer && $answer->time_spent ? round($answer->time_spent) . ' sec' : 'N/A'
                                ];
                            }
                        }
                        
                        // Format result data
                        $correctCount = $answers->where('is_correct', true)->count();
                        $wrongCount = $answers->where('is_correct', false)->count();
                        $totalQuestions = $answers->count();
                        
                        $result = (object)[
                            'score' => round($result->score),
                            'accuracy' => round($result->score),
                            'correct_count' => $correctCount,
                            'wrong_count' => $wrongCount,
                            'total_questions' => $totalQuestions,
                            'correct_rate' => $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0,
                            'wrong_rate' => $totalQuestions > 0 ? round(($wrongCount / $totalQuestions) * 100) : 0,
                            'time_spent' => $this->formatTime($result->time_spent),
                            'avg_time' => $totalQuestions > 0 ? round($result->time_spent / $totalQuestions) : 0,
                            'completed_at' => \Carbon\Carbon::parse($result->completed_at)->format('Y-m-d'),
                            'student_id' => $studentProfile->student_id ?? 'N/A'
                        ];
                        
                        // Build assessment object
                        $assessment = (object)[
                            'title' => $assessment->title,
                            'subject' => ucfirst($assessment->assessment_type)
                        ];
                        
                        // Calculate performance summary
                        $strongAreas = [];
                        $improvementAreas = [];
                        
                        $competencyStats = [];
                        foreach ($questions as $q) {
                            $comp = $q['category'];
                            if (!isset($competencyStats[$comp])) {
                                $competencyStats[$comp] = ['correct' => 0, 'total' => 0];
                            }
                            $competencyStats[$comp]['total']++;
                            if ($q['is_correct']) {
                                $competencyStats[$comp]['correct']++;
                            }
                        }
                        
                        foreach ($competencyStats as $comp => $stats) {
                            $percentage = $stats['total'] > 0 ? ($stats['correct'] / $stats['total']) * 100 : 0;
                            if ($percentage >= 75) {
                                $strongAreas[] = $comp;
                            } else {
                                $improvementAreas[] = $comp;
                            }
                        }
                        
                        $summary = (object)[
                            'strong_areas' => implode(', ', $strongAreas) ?: 'N/A',
                            'improvement_areas' => implode(', ', $improvementAreas) ?: 'N/A',
                            'recommendation' => $this->generateRecommendation($student, $result, $improvementAreas)
                        ];
                    }
                }
            }
        }
    } catch (\Exception $e) {
        // If there's any error, we'll use default data
        \Log::error('Error fetching assessment data: ' . $e->getMessage());
    }
    
    // If no real data found, use default sample data
    if (!$assessment || !$result) {
        $assessment = (object)[
            'title' => 'Algebra Basics Quiz',
            'subject' => 'Algebra'
        ];
        
        $result = (object)[
            'score' => 92,
            'accuracy' => 92,
            'correct_count' => 23,
            'wrong_count' => 2,
            'total_questions' => 25,
            'correct_rate' => 92,
            'wrong_rate' => 8,
            'time_spent' => '14 min 30 sec',
            'avg_time' => 34,
            'completed_at' => '2024-01-18',
            'student_id' => $studentProfile->student_id ?? '2024001'
        ];
        
        $summary = (object)[
            'strong_areas' => 'Linear Equations, Algebraic Expressions, Factoring, Radicals',
            'improvement_areas' => 'Quadratic Equations, Exponents',
            'recommendation' => $student->name . ' demonstrates excellent foundational skills in algebra with very fast solving times. However, they should review the concept of positive and negative roots in quadratic equations and the rules for multiplying exponents. Their quick pace suggests strong understanding, but attention to detail in these specific areas will help achieve perfect scores.'
        ];
    }
    // admin.teacher.sections.student-profile
    return view('admin.teacher.sections.review-assessment', compact(
        'student',
        'assessment',
        'result',
        'questions',
        'summary'
    ));
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

/**
 * Generate personalized recommendation based on performance
 */
private function generateRecommendation($student, $result, $improvementAreas)
{
    $score = $result->score;
    $name = $student->name;
    
    if ($score >= 90) {
        $performance = 'excellent foundational skills';
    } elseif ($score >= 75) {
        $performance = 'good understanding';
    } else {
        $performance = 'developing skills';
    }
    
    $recommendation = $name . ' demonstrates ' . $performance . ' in this assessment';
    
    if ($score >= 90) {
        $recommendation .= ' with very fast solving times';
    }
    
    if (!empty($improvementAreas)) {
        $recommendation .= '. However, they should review the concepts in: ' . implode(', ', $improvementAreas);
    }
    
    if ($score >= 85) {
        $recommendation .= '. Attention to detail in these specific areas will help achieve perfect scores.';
    } else {
        $recommendation .= '. Additional practice and review sessions are recommended to strengthen understanding.';
    }
    
    return $recommendation;
}
}