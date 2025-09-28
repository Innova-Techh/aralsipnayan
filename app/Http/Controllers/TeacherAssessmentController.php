<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherAssessmentController extends Controller
{
    public function index()
    {
        $teacher = Auth::guard('admin')->user();
        
        // Get teacher's sections from teacher_sections table
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        
        if (!$teacherProfile) {
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
                        'student_profile.section'
                    ])
                    ->get()
                    ->toArray();
            } else {
                $studentsData = [];
            }
        }
        
        // Get assessments created by this teacher
        $assessments = Assessment::where('created_by', $teacher->id)
            ->with(['assignments.student.studentProfile'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.teacher.assessments.index', compact('assessments', 'teacherSections', 'studentsData'));
    }

    public function create()
    {
        $teacher = Auth::guard('admin')->user();
        
        // Get teacher's sections from teacher_sections table
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        
        if (!$teacherProfile) {
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
                        'student_profile.section'
                    ])
                    ->get()
                    ->toArray();
            } else {
                $studentsData = [];
            }
        }
        
        return view('admin.teacher.assessments.create', compact('teacherSections', 'studentsData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:Number & Algebra,Measurement & Geometry,Data & Probability',
            'number_of_questions' => 'required|integer|min:5|max:50',
            'time_limit' => 'required|integer|min:10|max:120',
            'difficulty' => 'required|in:Easy,Medium,Hard,Mixed',
            'is_live_quiz' => 'boolean',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'sections' => 'array',
            'sections.*' => 'string',
            // Accept either an array (selected_students[]) or a CSV string in selected_students
            'selected_students' => 'nullable',
            'accommodations' => 'boolean'
        ]);

        // Debug authentication
        $teacherId = Auth::guard('admin')->id();
        if (!$teacherId) {
            return redirect()->back()->withErrors(['auth' => 'You must be logged in as a teacher to create assessments.']);
        }

        $assessment = Assessment::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'number_of_questions' => $request->number_of_questions,
            'time_limit' => $request->time_limit,
            'difficulty' => $request->difficulty,
            'is_live_quiz' => $request->boolean('is_live_quiz'),
            'available_from' => $request->available_from,
            'available_until' => $request->available_until,
            'created_by' => $teacherId,
            'status' => ($request->has('sections') || $request->has('selected_students')) ? 'Active' : 'Draft'
        ]);

        // Normalize selected students input (accept array or comma-separated string)
        $selectedStudentsInput = $request->input('selected_students');
        if (is_string($selectedStudentsInput)) {
            $selectedStudents = array_filter(array_map('trim', explode(',', $selectedStudentsInput)));
        } else {
            $selectedStudents = is_array($selectedStudentsInput) ? $selectedStudentsInput : [];
        }
        // Normalize to integer IDs and keep only existing users
        $selectedStudents = array_values(array_unique(array_map('intval', $selectedStudents)));
        if (!empty($selectedStudents)) {
            $existingIds = DB::table('users')->whereIn('id', $selectedStudents)->pluck('id')->toArray();
            $selectedStudents = array_values(array_intersect($selectedStudents, $existingIds));
        }

        if (!empty($selectedStudents)) {
            // If assigning to specific students, ensure no section-wide rows remain for this assessment
            AssessmentAssignment::where('assessment_id', $assessment->id)
                ->whereNull('student_id')
                ->delete();

            foreach ($selectedStudents as $studentId) {
                AssessmentAssignment::create([
                    'assessment_id' => $assessment->id,
                    'student_id' => $studentId,
                    'section' => null,
                    'accommodations' => $request->input('accommodations', false),
                    'assigned_at' => now(),
                    'due_date' => $request->available_until
                ]);
            }
        } elseif ($request->has('sections')) {
            // Otherwise, assign to entire sections
            foreach ((array) $request->sections as $section) {
                AssessmentAssignment::create([
                    'assessment_id' => $assessment->id,
                    'section' => $section,
                    'accommodations' => $request->input('accommodations', false),
                    'assigned_at' => now(),
                    'due_date' => $request->available_until
                ]);
            }
        }

        return redirect()->route('teacher.assessments')->with('success', 'Assessment created successfully!');
    }

    public function show(Assessment $assessment)
    {
        $assessment->load(['assignments.student.studentProfile', 'creator']);
        

        
        // If it's an AJAX request (from edit modal), return JSON
        if (request()->ajax()) {
            return response()->json([
                'assessment' => $assessment,
                'assignments' => $assessment->assignments
            ]);
        }
        
        return view('admin.teacher.assessments.show', compact('assessment'));
    }

    public function edit(Assessment $assessment)
    {
        // Return the edit view with assessment data
        // The view will handle the modal display
        return view('admin.teacher.assessments.edit', compact('assessment'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:Number & Algebra,Measurement & Geometry,Data & Probability',
            'number_of_questions' => 'required|integer|min:5|max:50',
            'time_limit' => 'required|integer|min:10|max:120',
            'difficulty' => 'required|in:Easy,Medium,Hard,Mixed',
            'is_live_quiz' => 'boolean',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
        ]);

        $assessment->update($request->only([
            'title', 'description', 'category', 'number_of_questions', 
            'time_limit', 'difficulty', 'is_live_quiz', 'available_from', 'available_until'
        ]));

        return redirect()->route('teacher.assessments')->with('success', 'Assessment updated successfully!');
    }

    public function removeAssignment(AssessmentAssignment $assignment)
    {
        // Verify the assignment belongs to a teacher's assessment
        $teacher = Auth::guard('admin')->user();
        $assessment = $assignment->assessment;
        
        if ($assessment->created_by !== $teacher->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $assignment->delete();
        
        return response()->json(['success' => true, 'message' => 'Assignment removed successfully']);
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();
        return redirect()->route('teacher.assessments')->with('success', 'Assessment deleted successfully!');
    }

    public function getStudents($section)
    {
        try {
            $students = DB::table('student_profile')
                ->join('users', 'student_profile.user_id', '=', 'users.id')
                ->where('student_profile.section', $section)
                ->where('users.status', 'active')
                ->select([
                    'student_profile.user_id',
                    'student_profile.firstname',
                    'student_profile.lastname',
                    'student_profile.section'
                ])
                ->get();
            
            return response()->json(['students' => $students]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch students', 'debug' => $e->getMessage()], 500);
        }
    }

    public function assign(Request $request)
    {
        try {
        $assessment = Assessment::findOrFail($request->assessment_id);
        
        // If section is provided, remove existing section-wide assignments
        if ($request->section) {
            AssessmentAssignment::where('assessment_id', $assessment->id)
                ->whereNull('student_id')
                ->where(function($q) use ($request) {
                    $q->where('section', $request->section)
                      ->orWhere('section', 'Section ' . $request->section);
                })
                ->delete();
        }
        
        // If specific students are selected
        if (!empty($request->student_ids)) {
            // Assign only to specific students
            foreach ($request->student_ids as $studentId) {
                AssessmentAssignment::create([
                    'assessment_id' => $assessment->id,
                    'student_id' => $studentId,
                    // Ensure student-specific rows are never treated as section-wide
                    'section' => null,
                    'accommodations' => $request->input('accommodations', false),
                    'assigned_at' => now(),
                    'due_date' => $assessment->available_until
                ]);
            }
            // Ensure assessment becomes visible to students
            if ($assessment->status !== 'Active') {
                $assessment->status = 'Active';
                $assessment->save();
            }
        } elseif ($request->section) {
            // Assign to entire section (no specific students selected)
            AssessmentAssignment::create([
                'assessment_id' => $assessment->id,
                'student_id' => null,
                'section' => $request->section,
                'accommodations' => $request->boolean('accommodations'),
                'assigned_at' => now(),
                'due_date' => $assessment->available_until
            ]);

            // Ensure assessment becomes visible to students
            if ($assessment->status !== 'Active') {
                $assessment->status = 'Active';
                $assessment->save();
            }
        }

            \Log::info('Assignment completed successfully');
            return response()->json(['success' => true, 'message' => 'Assessment assigned successfully!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Assignment failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to assign assessment', 'error' => $e->getMessage()], 500);
        }
    }

    // Student methods
    public function studentIndex()
    {
        $student = Auth::guard('student')->user();
        
        // Get student's section from student_profile
        $studentProfile = DB::table('student_profile')->where('user_id', $student->id)->first();
        $studentSection = $studentProfile ? $studentProfile->section : null;
        
        // Get assessments assigned to this student
        $assignments = AssessmentAssignment::where(function($query) use ($student, $studentSection) {
            // First check for assignments specifically to this student
            $query->where('student_id', $student->id)
                  // Then check for section-wide assignments (where student_id is null)
                  ->orWhere(function($q) use ($studentSection) {
                      // Handle both "A" and "Section A" formats
                      $q->where(function($subQ) use ($studentSection) {
                          $subQ->where('section', $studentSection)
                               ->orWhere('section', 'Section ' . $studentSection);
                      })
                      ->whereNull('student_id'); // Only section-wide assignments
                  });
        })
        ->with(['assessment' => function($query) {
            $query->where('status', 'Active');
        }])
        ->whereHas('assessment', function($query) {
            $query->where('status', 'Active');
        })
        ->get();

        return view('student.assessments.index', compact('assignments'));
    }

    public function studentShow(Assessment $assessment)
    {
        // Check if student has access to this assessment
        $student = Auth::guard('student')->user();
        
        // Get student's section from student_profile
        $studentProfile = DB::table('student_profile')->where('user_id', $student->id)->first();
        $studentSection = $studentProfile ? $studentProfile->section : null;
        
        $assignment = AssessmentAssignment::where('assessment_id', $assessment->id)
            ->where(function($query) use ($student, $studentSection) {
                // First check for assignments specifically to this student
                $query->where('student_id', $student->id)
                      // Then check for section-wide assignments (where student_id is null)
                      ->orWhere(function($q) use ($studentSection) {
                          // Handle both "A" and "Section A" formats
                          $q->where(function($subQ) use ($studentSection) {
                              $subQ->where('section', $studentSection)
                                   ->orWhere('section', 'Section ' . $studentSection);
                          })
                          ->whereNull('student_id'); // Only section-wide assignments
                      });
            })
            ->first();

        if (!$assignment) {
            abort(403, 'You do not have access to this assessment.');
        }

        return view('student.assessments.show', compact('assessment', 'assignment'));
    }
}
