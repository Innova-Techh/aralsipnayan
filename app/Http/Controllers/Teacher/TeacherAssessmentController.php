<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAssignment;
use App\Models\QuizResult;
use App\Models\TeacherAssessmentSession;
use App\Models\TeacherQuizResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
        
        // Get available topics for each category
        $availableTopics = $this->getAvailableTopics();
        
        return view('admin.teacher.assessments.create', compact('teacherSections', 'studentsData', 'availableTopics'));
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
            'accommodations' => 'boolean',
            'question_source' => 'required|in:question_bank',
            'selected_bank_questions' => 'nullable|string',
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

        // Handle selected bank questions
        if ($request->selected_bank_questions) {
            $bankQuestionIds = explode(',', $request->selected_bank_questions);
            $bankQuestionIds = array_filter($bankQuestionIds); // Remove empty values
            if (!empty($bankQuestionIds)) {
                // Bank questions from JSON files don't need database verification
                // They are valid as long as they follow the correct ID format
                $validBankQuestions = array_filter($bankQuestionIds, function($questionId) {
                    // Validate question ID format (e.g., NA-B-001, MG-I-002, DP-A-003)
                    return preg_match('/^(NA|MG|DP)-(B|I|A)-\d{3}$/', $questionId);
                });
                
                if (!empty($validBankQuestions)) {
                    $this->assignQuestionsToAssessment($assessment->id, $validBankQuestions);
                }
            }
        }

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
            // Assign directly to specific students
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
                    'due_date' => $request->available_until,
                    'status' => 'Assigned',
                ]);
            }
        
        
        } elseif ($request->has('sections')) {
        
            // Delete any existing individual student assignments for these sections
            $deleteCount = AssessmentAssignment::where('assessment_id', $assessment->id)
                ->whereIn('section', (array) $request->sections)
                ->delete();
                
        
            foreach ((array) $request->sections as $section) {
                \Log::info('Processing section', ['section' => $section]);
        
                // Get all students in this section
                $students = DB::table('student_profile')
                    ->join('users', 'student_profile.user_id', '=', 'users.id')
                    ->where('student_profile.section', $section)
                    ->select('users.id as student_id')
                    ->get();
        
        
                if ($students->isEmpty()) {
                    \Log::warning('No students found for section', [
                        'section' => $section,
                        'assessment_id' => $assessment->id,
                    ]);
                    continue;
                }
        
                $createdCount = 0;
                foreach ($students as $student) {
                    AssessmentAssignment::create([
                        'assessment_id' => $assessment->id,
                        'student_id' => $student->student_id,
                        'section' => $section,
                        'accommodations' => $request->input('accommodations', false),
                        'assigned_at' => now(),
                        'due_date' => $request->available_until,
                        'status' => 'Assigned',
                    ]);
                    $createdCount++;
                }
        
            }
            
        } else {
            \Log::warning('No assignment conditions met', [
                'has_selectedStudents' => !empty($selectedStudents),
                'has_sections' => $request->has('sections'),
                'assessment_id' => $assessment->id
            ]);
        }

        return redirect()->route('teacher.assessments')->with('success', 'Assessment created successfully!');
    }

    /**
     * Load questions from the JSON files based on filters
     */
    public function loadQuestions(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'topic' => 'nullable|string',
            'difficulty' => 'nullable|string'
        ]);

        // Map category names to file prefixes
        $categoryMap = [
            'Number & Algebra' => 'number_algebra',
            'Measurement & Geometry' => 'measurement_geometry',
            'Data & Probability' => 'data_probability'
        ];

        $categoryPrefix = $categoryMap[$request->category] ?? null;
        if (!$categoryPrefix) {
            return response()->json(['questions' => []]);
        }

        // Map difficulty levels to file suffixes
        $difficultyMap = [
            'Easy' => 'beginner',
            'Medium' => 'intermediate',
            'Hard' => 'advanced'
        ];

        $questions = [];
        $basePath = base_path("database/data/{$categoryPrefix}");

        // Determine which difficulty files to load
        $difficultiesToLoad = [];
        if ($request->difficulty && isset($difficultyMap[$request->difficulty])) {
            $difficultiesToLoad[] = $difficultyMap[$request->difficulty];
        } else {
            // Load all difficulties if no specific difficulty is requested
            $difficultiesToLoad = ['beginner', 'intermediate', 'advanced'];
        }

        // Load questions from the appropriate JSON files
        foreach ($difficultiesToLoad as $difficulty) {
            $filePath = "{$basePath}/{$categoryPrefix}_{$difficulty}.json";
            
            if (file_exists($filePath)) {
                $fileContent = file_get_contents($filePath);
                $fileQuestions = json_decode($fileContent, true);
                
                if ($fileQuestions && is_array($fileQuestions)) {
                    // Apply topic filter if provided
                    if ($request->topic) {
                        $fileQuestions = array_filter($fileQuestions, function($question) use ($request) {
                            return isset($question['topic_tag']) && $question['topic_tag'] === $request->topic;
                        });
                    }
                    
                    $questions = array_merge($questions, $fileQuestions);
                }
            }
        }

        // Limit results to prevent overwhelming the UI
        $questions = array_slice($questions, 0, 50);

        // Ensure questions have the required fields and normalize the structure
        $questions = array_map(function($question) {
            return [
                'question_id' => $question['question_id'] ?? '',
                'competency' => $question['competency'] ?? '',
                'difficulty_level' => ucfirst($question['difficulty_level'] ?? ''),
                'topic_tag' => $question['topic_tag'] ?? '',
                'question_text' => $question['question_text'] ?? '',
                'question_type' => $question['question_type'] ?? 'multiple_choice',
                'choice_a' => $question['choice_a'] ?? '',
                'choice_b' => $question['choice_b'] ?? '',
                'choice_c' => $question['choice_c'] ?? '',
                'choice_d' => $question['choice_d'] ?? '',
                'correct_answer' => $question['correct_answer'] ?? '',
                'hint_text' => $question['hint_text'] ?? '',
                'explanation' => $question['explanation'] ?? ''
            ];
        }, $questions);

        return response()->json(['questions' => $questions]);
    }

    /**
     * Get available topics from JSON files for each category
     */
    private function getAvailableTopics()
    {
        $categories = [
            'Number & Algebra' => 'number_algebra',
            'Measurement & Geometry' => 'measurement_geometry',
            'Data & Probability' => 'data_probability'
        ];

        $availableTopics = [];

        foreach ($categories as $categoryName => $categoryPrefix) {
            $topics = [];
            $basePath = base_path("database/data/{$categoryPrefix}");
            
            // Check beginner file for topics (assuming all files have similar topic distribution)
            $filePath = "{$basePath}/{$categoryPrefix}_beginner.json";
            
            if (file_exists($filePath)) {
                $fileContent = file_get_contents($filePath);
                $questions = json_decode($fileContent, true);
                
                if ($questions && is_array($questions)) {
                    // Get first 100 questions to sample topics
                    $sampleQuestions = array_slice($questions, 0, 100);
                    foreach ($sampleQuestions as $question) {
                        if (isset($question['topic_tag']) && !in_array($question['topic_tag'], $topics)) {
                            $topics[] = $question['topic_tag'];
                        }
                    }
                }
            }
            
            sort($topics); // Sort alphabetically
            $availableTopics[$categoryName] = $topics;
        }

        return $availableTopics;
    }


    /**
     * Assign questions to a teacher assessment
     * All questions in the same assessment share the same pool_id
     */
    private function assignQuestionsToAssessment($assessmentId, $questionIds)
    {
        $teacherId = Auth::guard('admin')->id();
        
        // Generate ONE pool_id for all questions in this assessment
        $poolId = Str::uuid()->toString();
        
        foreach ($questionIds as $index => $questionId) {
            DB::table('teacher_assessment_questions')->insert([
                'pool_id' => $poolId,
                'teacher_assessment_id' => $assessmentId,
                'question_id' => $questionId,
                'question_order' => $index + 1,
                'shuffled_order' => null, // Will be set during quiz start
                'is_answered' => false,
                'is_current' => false,
                'created_by' => $teacherId,
                'added_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Fisher-Yates shuffle algorithm for randomizing questions
     */
    private function fisherYatesShuffle($array)
    {
        $count = count($array);
        for ($i = $count - 1; $i > 0; $i--) {
            $j = rand(0, $i);
            // Swap elements
            $temp = $array[$i];
            $array[$i] = $array[$j];
            $array[$j] = $temp;
        }
        return $array;
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
    
            Log::debug('Assign request payload', [
                'assessment_id' => $assessment->id,
                'student_ids' => $request->student_ids,
                'section' => $request->section,
                'sections' => $request->sections,
            ]);
    
            // 🧩 If specific students are selected
            if (!empty($request->student_ids)) {
                foreach ($request->student_ids as $studentId) {
                    AssessmentAssignment::updateOrCreate(
                        [
                            'assessment_id' => $assessment->id,
                            'student_id' => $studentId,
                        ],
                        [
                            'section' => null,
                            'accommodations' => $request->boolean('accommodations'),
                            'assigned_at' => now(),
                            'due_date' => $assessment->available_until,
                            'status' => 'Assigned',
                        ]
                    );
                }
            }
    
            // 🧩 If a section (or multiple) is selected
            if ($request->has('sections') || $request->section) {
                $sections = $request->has('sections')
                    ? (array) $request->sections
                    : [(string) $request->section];
    
                foreach ($sections as $section) {
                    // 🧠 Get all students in this section
                    $students = DB::table('student_profile')
                        ->join('users', 'student_profile.user_id', '=', 'users.id')
                        ->where('student_profile.section', $section)
                        ->select('users.id as student_id')
                        ->get();
    
                    if ($students->isEmpty()) {
                        Log::warning('No students found for section', [
                            'section' => $section,
                            'assessment_id' => $assessment->id,
                        ]);
                        continue;
                    }
    
                    foreach ($students as $student) {
                        AssessmentAssignment::updateOrCreate(
                            [
                                'assessment_id' => $assessment->id,
                                'student_id' => $student->student_id,
                            ],
                            [
                                'section' => $section,
                                'accommodations' => $request->boolean('accommodations'),
                                'assigned_at' => now(),
                                'due_date' => $assessment->available_until,
                                'status' => 'Assigned',
                            ]
                        );
                    }
    
                    Log::info('Section assigned successfully', [
                        'section' => $section,
                        'student_count' => $students->count(),
                        'assessment_id' => $assessment->id,
                    ]);
                }
            }
    
            // 🟢 No section or student selected
            if (empty($request->student_ids) && !$request->has('sections') && !$request->section) {
                Log::warning('No assignment conditions met', [
                    'has_student_ids' => !empty($request->student_ids),
                    'has_sections' => $request->has('sections'),
                    'has_section' => $request->section,
                    'assessment_id' => $assessment->id,
                ]);
            }
    
            // ✅ Ensure assessment becomes visible
            if ($assessment->status !== 'Active') {
                $assessment->status = 'Active';
                $assessment->save();
            }
    
            Log::info('Assignment completed successfully');
            return response()->json(['success' => true, 'message' => 'Assessment assigned successfully!']);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Assignment failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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

        // Check for completed assessments and update status in memory
        $completedAssessmentIds = QuizResult::where('student_id', $student->id)
            ->pluck('assessment_id')
            ->toArray();

        foreach ($assignments as $assignment) {
            if (in_array($assignment->assessment_id, $completedAssessmentIds)) {
                $assignment->status = 'Completed';
            }
        }

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

        // Check if student has already completed this assessment
        $quizResult = QuizResult::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->first();

        // If quiz result exists, mark assignment as completed for this view
        if ($quizResult) {
            $assignment->status = 'Completed';
        }

        return view('student.assessments.show', compact('assessment', 'assignment', 'quizResult'));
    }

    /**
     * Start the quiz for a teacher assessment
     */
    public function startQuiz(Assessment $assessment)
    {
        $student = Auth::guard('student')->user();
        
        // Check if student has access to this assessment
        $studentProfile = DB::table('student_profile')->where('user_id', $student->id)->first();
        $studentSection = $studentProfile ? $studentProfile->section : null;
        
        $assignment = AssessmentAssignment::where('assessment_id', $assessment->id)
            ->where(function($query) use ($student, $studentSection) {
                $query->where('student_id', $student->id)
                      ->orWhere(function($q) use ($studentSection) {
                          $q->where(function($subQ) use ($studentSection) {
                              $subQ->where('section', $studentSection)
                                   ->orWhere('section', 'Section ' . $studentSection);
                          })
                          ->whereNull('student_id');
                      });
            })
            ->first();

        if (!$assignment) {
            abort(403, 'You do not have access to this assessment.');
        }
        
        // Check if assessment is already completed
        if ($assignment->status === 'Completed') {
            return redirect()->route('teacher-assessments.show', $assessment->id)
                ->with('info', 'This assessment has already been completed.');
        }
        
        // Check for existing active session
        $existingSession = TeacherAssessmentSession::where('teacher_assessment_id', $assessment->id)
            ->where('user_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingSession) {
            // Resume session
            $shuffledQuestions = $existingSession->questions_json;
            $sessionId = $existingSession->session_id;
            
            // Store quiz session data in Laravel session
            session([
                'quiz_assessment_id' => $assessment->id,
                'quiz_session_id' => $sessionId,
                'quiz_questions' => $shuffledQuestions,
                'current_question_index' => $existingSession->current_question_index,
                'quiz_start_time' => $existingSession->started_at,
                'current_bkt_probability' => $existingSession->final_bkt_probability ?? 0.5 // Use stored probability if available
            ]);
            
            // Re-populate answered questions for frontend
            // We need to pass this to the view so the frontend knows which questions are answered
            // The frontend uses sessionStorage 'answeredQuestions', we might need to seed it
            
            // Populate questionDetails from saved questions
            $questionDetails = $shuffledQuestions;
            
            // Get answered question IDs to restore frontend state
            $answeredQuestionIds = TeacherQuizResponse::where('session_id', $sessionId)
                ->pluck('question_id')
                ->toArray();
            
            $currentQuestionIndex = $existingSession->current_question_index;
            
            return view('student.assessments.quiz', compact('assessment', 'assignment', 'questionDetails', 'answeredQuestionIds', 'currentQuestionIndex'));
        }

        // Get questions for this assessment
        $questions = DB::table('teacher_assessment_questions')
            ->where('teacher_assessment_id', $assessment->id)
            ->orderBy('question_order')
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->route('teacher-assessments.show', $assessment->id)
                ->withErrors(['error' => 'No questions found for this assessment.']);
        }

        // Load question details from JSON files or database
        $questionDetails = [];
        foreach ($questions as $question) {
            $questionDetail = $this->getQuestionDetails($question->question_id);
            if ($questionDetail) {
                $questionDetails[] = array_merge($questionDetail, [
                    'pool_id' => $question->pool_id,
                    'question_order' => $question->question_order,
                    'is_answered' => $question->is_answered
                ]);
            }
        }

        // Apply Fisher-Yates shuffle to randomize questions
        $shuffledQuestions = $this->fisherYatesShuffle($questionDetails);

        // Create teacher assessment session
        $sessionId = 'TAS-' . $student->id . '-' . $assessment->id . '-' . time();
        
        // Determine competency from assessment category
        $competencyMap = [
            'Number & Algebra' => 'number_algebra',
            'Measurement & Geometry' => 'measurement_geometry',
            'Data & Probability' => 'data_probability'
        ];
        $competency = $competencyMap[$assessment->category] ?? 'mixed';
        
        // Determine difficulty level
        $difficultyMap = [
            'Easy' => 'beginner',
            'Medium' => 'intermediate',
            'Hard' => 'advanced',
            'Mixed' => 'mixed'
        ];
        $difficultyLevel = $difficultyMap[$assessment->difficulty] ?? 'mixed';

        $session = TeacherAssessmentSession::create([
            'session_id' => $sessionId,
            'user_id' => $student->id,
            'teacher_assessment_id' => $assessment->id,
            'session_type' => 'teacher_created',
            'competency' => $competency,
            'difficulty_level' => $difficultyLevel,
            'total_questions' => count($shuffledQuestions),
            'questions_json' => $shuffledQuestions,
            'current_question_index' => 0,
            'time_limit_minutes' => $assessment->time_limit,
            'total_time_allowed_seconds' => $assessment->time_limit * 60,
            'countdown_started_at' => now(),
            'countdown_expires_at' => now()->addMinutes($assessment->time_limit),
            'time_remaining_seconds' => $assessment->time_limit * 60,
            'initial_bkt_probability' => 0.5, // Starting probability
            'status' => 'in_progress',
            'started_at' => now()
        ]);

        // Store quiz session data in Laravel session
        session([
            'quiz_assessment_id' => $assessment->id,
            'quiz_session_id' => $sessionId,
            'quiz_questions' => $shuffledQuestions,
            'current_question_index' => 0,
            'quiz_start_time' => now(),
            'current_bkt_probability' => 0.5
        ]);
        
        $answeredQuestionIds = [];
        $currentQuestionIndex = 0;
        
        return view('student.assessments.quiz', compact('assessment', 'assignment', 'questionDetails', 'answeredQuestionIds', 'currentQuestionIndex'));
    }

    /**
     * Submit an answer for a question
     */
    public function submitAnswer(Request $request, Assessment $assessment)
    {
        $request->validate([
            'question_id' => 'required|string',
            'answer' => 'required|string',
            'time_taken' => 'required|integer|min:1'
        ]);

        $student = Auth::guard('student')->user();
        
        // Verify quiz session
        $sessionId = session('quiz_session_id');
        if (!$sessionId || session('quiz_assessment_id') != $assessment->id) {
            return response()->json(['success' => false, 'message' => 'Invalid quiz session.']);
        }

        // Get session from database
        $session = TeacherAssessmentSession::where('session_id', $sessionId)->first();
        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Session not found.']);
        }

        $questions = session('quiz_questions', []);
        $currentIndex = session('current_question_index', 0);

        // Find the question
        $question = null;
        foreach ($questions as $q) {
            if ($q['question_id'] === $request->question_id) {
                $question = $q;
                break;
            }
        }

        if (!$question) {
            return response()->json(['success' => false, 'message' => 'Question not found.']);
        }

        // Check if answer is correct
        $isCorrect = $request->answer === $question['correct_answer'];

        // Get current BKT probability
        $currentBKT = session('current_bkt_probability', 0.5);
        
        // Calculate new BKT probability
        $newBKT = $session->updateBKTProbability($isCorrect, $currentBKT);
        
        // Determine difficulty level from question_id
        $difficultyMap = [
            'B' => 'beginner',
            'I' => 'intermediate',
            'A' => 'advanced'
        ];
        preg_match('/^(NA|MG|DP)-(B|I|A)-(\d{3})$/', $question['question_id'], $matches);
        $difficultyLevel = $difficultyMap[$matches[2]] ?? 'intermediate';
        
        // Calculate time limit for this difficulty
        $timeLimits = [
            'beginner' => 30,
            'intermediate' => 45,
            'advanced' => 60
        ];
        $timeLimit = $timeLimits[$difficultyLevel];
        
        // Calculate points
        $responseModel = new TeacherQuizResponse();
        $points = $responseModel->calculatePoints($isCorrect, $request->time_taken, $timeLimit, $difficultyLevel);

        // Save response to database
        TeacherQuizResponse::create([
            'session_id' => $sessionId,
            'pool_id' => $question['pool_id'],
            'question_id' => $request->question_id,
            'student_id' => $student->id,
            'student_answer' => $request->answer,
            'correct_answer' => $question['correct_answer'],
            'is_correct' => $isCorrect,
            'points_earned' => $points,
            'time_taken' => $request->time_taken,
            'bkt_probability_before' => $currentBKT,
            'bkt_probability_after' => $newBKT,
            'difficulty_level' => $difficultyLevel,
            'competency' => $session->competency,
            'topic_tag' => $question['topic_tag'] ?? null,
            'answered_at' => now()
        ]);

        // Update session
        $session->increment('questions_answered');
        if ($isCorrect) {
            $session->increment('correct_answers');
            $session->increment('total_points_earned', $points);
        } else {
            $session->increment('incorrect_answers');
        }
        $session->current_question_index = $currentIndex + 1;
        $session->save();

        // Update Laravel session
        session([
            'current_question_index' => $currentIndex + 1,
            'current_bkt_probability' => $newBKT
        ]);
        
        // Debug logging
        \Log::info('Answer submitted', [
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'question_id' => $request->question_id,
            'is_correct' => $isCorrect,
            'points' => $points,
            'bkt_before' => $currentBKT,
            'bkt_after' => $newBKT
        ]);

        // Move to next question
        $nextIndex = $currentIndex + 1;

        $isLastQuestion = $nextIndex >= count($questions);

        return response()->json([
            'success' => true,
            'is_correct' => $isCorrect,
            'is_last_question' => $isLastQuestion,
            'next_question_index' => $nextIndex
        ]);
    }

    /**
     * Complete the quiz
     */
    public function completeQuiz(Request $request, Assessment $assessment)
    {
        $student = Auth::guard('student')->user();

        // Verify quiz session
        $sessionId = session('quiz_session_id');
        if (!$sessionId || session('quiz_assessment_id') != $assessment->id) {
            return redirect()->route('teacher-assessments.show', $assessment->id)
                ->withErrors(['error' => 'Invalid quiz session.']);
        }

        // Get session from database
        $session = TeacherAssessmentSession::where('session_id', $sessionId)->first();
        if (!$session) {
            return redirect()->route('teacher-assessments.show', $assessment->id)
                ->withErrors(['error' => 'Session not found.']);
        }

        // Calculate total time from sum of response times (active time)
        $totalTime = now()->diffInSeconds($session->started_at);

        // Calculate accuracy percentage
        $accuracyPercentage = $session->total_questions > 0 
            ? round(($session->correct_answers / $session->total_questions) * 100, 2) 
            : 0;

        // Calculate average response time
        $avgResponseTime = $session->questions_answered > 0
            ? round($totalTime / $session->questions_answered, 3)
            : 0;

        // Get final BKT probability from session
        $finalBKT = session('current_bkt_probability', 0.5);

        // Calculate final mastery score
        $finalMasteryScore = $session->calculateFinalMasteryScore();

        // Update session with final metrics
        $session->update([
            'accuracy_percentage' => $accuracyPercentage,
            'average_response_time' => $avgResponseTime,
            'final_bkt_probability' => $finalBKT,
            'final_mastery_score' => $finalMasteryScore,
            'status' => 'completed',
            'completed_at' => now()
        ]);

        // Store results in quiz_results table (without answers)
        QuizResult::updateOrCreate(
            [
                'student_id' => $student->id,
                'assessment_id' => $assessment->id,
            ],
            [
                'session_id' => $sessionId,
                'score' => $session->correct_answers,
                'total_questions' => $session->total_questions,
                'percentage' => $accuracyPercentage,
                'time_taken' => $totalTime,
                'completed_at' => now(),
            ]
        );

        // Update assignment status
        $studentProfile = DB::table('student_profile')
            ->where('user_id', $student->id)
            ->first();

        $studentSection = $studentProfile ? $studentProfile->section : null;

        // IN COMPLETION METHOD - Replace your current query:
        $assignment = AssessmentAssignment::where('assessment_id', $assessment->id)
        ->where(function ($query) use ($student, $studentSection) {
            $query->where('student_id', $student->id)
                ->orWhere(function ($sectionQuery) use ($studentSection) {
                    $sectionQuery->where(function ($nameQuery) use ($studentSection) {
                        $nameQuery->where('section', $studentSection)
                                    ->orWhere('section', 'Section ' . $studentSection);
                    })
                    ->whereNull('student_id');
                });
        })
        ->orderByRaw('CASE WHEN student_id IS NOT NULL THEN 0 ELSE 1 END') // Student-specific first
        ->first(); // Use first() instead of get()

        if ($assignment) {
            Log::info('Updating assignment status after quiz completion', [
                'assignment_id' => $assignment->id,
                'student_id' => $assignment->student_id,
                'section' => $assignment->section,
                'from_status' => $assignment->status,
            ]);
            $assignment->update(['status' => 'Completed']);
        }

        if (!$assignment) {
            Log::warning('No assignment record matched for completion update', [
                'student_id' => $student->id,
                'section' => $studentSection,
                'assessment_id' => $assessment->id,
            ]);
        } else {
            Log::info('Updating assignment status after quiz completion', [
                'assignment_id' => $assignment->id,
                'student_id' => $assignment->student_id,
                'section' => $assignment->section,
                'from_status' => $assignment->status,
                'assessment_id' => $assessment->id,
            ]);
            $assignment->update(['status' => 'Completed']);
        }

        // Clear quiz session
        session()->forget([
            'quiz_assessment_id', 'quiz_session_id', 'quiz_questions', 'current_question_index',
            'quiz_start_time', 'current_bkt_probability'
        ]);

        // Redirect with success message
        return redirect()->route('teacher-assessments.show', $assessment->id)
            ->with('success', "Quiz completed! Score: {$session->correct_answers}/{$session->total_questions} ({$accuracyPercentage}%) | Points: {$session->total_points_earned}");
    }

    /**
     * Show all questions for a teacher assessment
     */
    public function showQuestions(Assessment $assessment)
    {
        $teacher = Auth::guard('admin')->user();
        
        // Verify teacher owns this assessment
        if ($assessment->created_by !== $teacher->id) {
            abort(403, 'You do not have permission to view these questions.');
        }

        // Get questions for this assessment
        $questions = DB::table('teacher_assessment_questions')
            ->where('teacher_assessment_id', $assessment->id)
            ->orderBy('question_order')
            ->get();

        // Load question details from JSON files
        $questionDetails = [];
        foreach ($questions as $question) {
            $questionDetail = $this->getQuestionDetails($question->question_id);
            if ($questionDetail) {
                $questionDetails[] = array_merge($questionDetail, [
                    'pool_id' => $question->pool_id,
                    'question_order' => $question->question_order,
                    'created_at' => $question->created_at
                ]);
            }
        }

        return view('admin.teacher.assessments.questions', compact('assessment', 'questionDetails'));
    }

    /**
     * Allow student to retake a completed assessment
     */
    public function retakeQuiz(Assessment $assessment)
    {
        $student = Auth::guard('student')->user();
        Log::info('Retake requested', [
            'student_id' => $student ? $student->id : null,
            'assessment_id' => $assessment->id,
        ]);

        // Verify student access
        $studentProfile = DB::table('student_profile')->where('user_id', $student->id)->first();
        $studentSection = $studentProfile ? $studentProfile->section : null;

        // IN RETAKE METHOD - Replace your current query:
        $assignment = AssessmentAssignment::where('assessment_id', $assessment->id)
        ->where(function($query) use ($student, $studentSection) {
            $query->where('student_id', $student->id)
                ->orWhere(function($q) use ($studentSection) {
                    $q->where(function($subQ) use ($studentSection) {
                        $subQ->where('section', $studentSection)
                            ->orWhere('section', 'Section ' . $studentSection);
                    })
                    ->whereNull('student_id');
                });
        })
        ->orderByRaw('CASE WHEN student_id IS NOT NULL THEN 0 ELSE 1 END') // Student-specific first
        ->first();

        if (!$assignment) {
            Log::warning('Retake blocked: no assignment record', [
                'student_id' => $student->id,
                'assessment_id' => $assessment->id,
                'section' => $studentSection,
            ]);
            abort(403, 'You do not have access to this assessment.');
        }

        if ($assignment->status !== 'Completed') {
            Log::info('Retake blocked: assignment not completed', [
                'assignment_id' => $assignment->id,
                'current_status' => $assignment->status,
            ]);
            return redirect()->route('teacher-assessments.show', $assessment->id)
                ->with('info', 'This assessment has not been completed yet.');
        }

        // Archive the old completed session
        $oldSession = TeacherAssessmentSession::where('teacher_assessment_id', $assessment->id)
            ->where('user_id', $student->id)
            ->where('status', 'completed')
            ->latest()
            ->first();

        if ($oldSession) {
            Log::info('Archiving old session before retake', [
                'session_id' => $oldSession->session_id,
                'status' => $oldSession->status,
            ]);
            $oldSession->update([
                'status' => 'abandoned',
                'session_notes' => 'Archived for retake on ' . now()->toDateTimeString(),
            ]);
        } else {
            Log::warning('No previous session found to archive for retake', [
                'student_id' => $student->id,
                'assessment_id' => $assessment->id,
            ]);
        }

        // Reset assignment status to allow a new attempt
        $assignment->status = 'Assigned';
        $assignment->save();
        Log::info('Assignment reopened for retake', [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
        ]);

        // Delete previous quiz result so the student can start fresh
        QuizResult::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->delete();

        // Clear any stored quiz session data
        session()->forget([
            'quiz_assessment_id',
            'quiz_session_id',
            'quiz_questions',
            'current_question_index',
            'quiz_start_time',
            'current_bkt_probability',
            'quiz_answers',
        ]);

        Log::info('Retake flow completed, redirecting to quiz start', [
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
        ]);

        return redirect()->route('teacher-assessments.start', $assessment->id)
            ->with('success', 'Assessment reset! You can now retake the quiz.');
    }

    /**
     * Show detailed results for a completed assessment
     */
    public function results(Assessment $assessment)
    {
        $student = Auth::guard('student')->user();

        // Get the latest completed session
        $session = TeacherAssessmentSession::where('teacher_assessment_id', $assessment->id)
            ->where('user_id', $student->id)
            ->where('status', 'completed')
            ->latest()
            ->first();

        if (!$session) {
            return redirect()->route('teacher-assessments.show', $assessment->id)
                ->with('error', 'No completed session found for this assessment.');
        }

        // Get quiz result
        $quizResult = QuizResult::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->first();

        // Get detailed responses with question text
        $responses = TeacherQuizResponse::where('session_id', $session->session_id)
            ->orderBy('created_at')
            ->get();

        // Fetch question details for each response
        $detailedResponses = [];
        foreach ($responses as $response) {
            $questionDetail = $this->getQuestionDetails($response->question_id);
            if ($questionDetail) {
                $detailedResponses[] = array_merge($response->toArray(), [
                    'question_text' => $questionDetail['question_text'], // Corrected key from 'question' to 'question_text'
                    'choices' => [ // Assuming choices are structured like this in getQuestionDetails
                        'a' => $questionDetail['choice_a'] ?? null,
                        'b' => $questionDetail['choice_b'] ?? null,
                        'c' => $questionDetail['choice_c'] ?? null,
                        'd' => $questionDetail['choice_d'] ?? null,
                    ],
                    'correct_answer_text' => $questionDetail['correct_answer'],
                    'explanation' => $questionDetail['explanation'] ?? null
                ]);
            }
        }

        return view('student.assessments.results', compact('assessment', 'session', 'quizResult', 'detailedResponses'));
    }

    /**
     * Get question details from JSON files or database
     */
    private function getQuestionDetails($questionId)
    {

        // Check JSON files for bank questions
        $categoryMap = [
            'NA' => 'number_algebra',
            'MG' => 'measurement_geometry', 
            'DP' => 'data_probability'
        ];

        $difficultyMap = [
            'B' => 'beginner',
            'I' => 'intermediate',
            'A' => 'advanced'
        ];

        // Parse question ID (e.g., NA-B-001)
        if (preg_match('/^(NA|MG|DP)-(B|I|A)-(\d{3})$/', $questionId, $matches)) {
            $categoryPrefix = $categoryMap[$matches[1]];
            $difficulty = $difficultyMap[$matches[2]];
            
            $filePath = base_path("database/data/{$categoryPrefix}/{$categoryPrefix}_{$difficulty}.json");
            
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $questions = json_decode($content, true);
                
                if ($questions) {
                    foreach ($questions as $question) {
                        if ($question['question_id'] === $questionId) {
                            return [
                                'question_id' => $question['question_id'],
                                'question_text' => $question['question_text'],
                                'question_type' => $question['question_type'],
                                'choice_a' => $question['choice_a'] ?? '',
                                'choice_b' => $question['choice_b'] ?? '',
                                'choice_c' => $question['choice_c'] ?? '',
                                'choice_d' => $question['choice_d'] ?? '',
                                'correct_answer' => $question['correct_answer'],
                                'hint_text' => $question['hint_text'] ?? '',
                                'explanation' => $question['explanation'] ?? ''
                            ];
                        }
                    }
                }
            }
        }

        return null;
    }
}
