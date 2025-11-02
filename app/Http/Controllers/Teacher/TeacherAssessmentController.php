<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAssignment;
use App\Models\QuizResult;
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
            'question_source' => 'required|in:question_bank,create_custom,mixed',
            'selected_bank_questions' => 'nullable|string',
            'custom_questions' => 'nullable|string'
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

        // Handle custom questions
        if ($request->custom_questions) {
            $customQuestions = json_decode($request->custom_questions, true);
            if ($customQuestions) {
                $this->storeCustomQuestions($customQuestions, $teacherId);
                $this->assignQuestionsToAssessment($assessment->id, array_column($customQuestions, 'id'));
            }
        }

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
     * Store custom questions in the database
     */
    private function storeCustomQuestions($customQuestions, $teacherId)
    {
        foreach ($customQuestions as $question) {
            // Map category to competency
            $competencyMap = [
                'Number & Algebra' => 'number_algebra',
                'Measurement & Geometry' => 'measurement_geometry',
                'Data & Probability' => 'data_probability'
            ];

            $category = request('category');
            $competency = $competencyMap[$category] ?? 'number_algebra';

            // Determine difficulty level based on points or set default
            $difficultyLevel = 'intermediate'; // Default
            if ($question['base_points'] <= 5) {
                $difficultyLevel = 'beginner';
            } elseif ($question['base_points'] >= 15) {
                $difficultyLevel = 'advanced';
            }

            $questionData = [
                'question_id' => $question['id'],
                'competency' => $competency,
                'difficulty_level' => $difficultyLevel,
                'topic_tag' => $question['topic_tag'],
                'question_text' => $question['question_text'],
                'question_type' => $question['question_type'],
                'correct_answer' => $question['correct_answer'],
                'hint_text' => $question['hint_text'] ?? null,
                'explanation' => $question['explanation'] ?? null,
                'max_allowed_time' => 60, // Default 60 seconds
                'estimated_difficulty_weight' => 1.0,
                'question_source' => 'custom',
                'is_active' => true,
                'usage_count' => 0,
                'success_rate' => 0.0000,
                'base_points' => $question['base_points'],
                'created_by' => $teacherId,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Add multiple choice options if applicable
            if ($question['question_type'] === 'multiple_choice' && isset($question['choices'])) {
                $questionData['choice_a'] = $question['choices']['A'] ?? null;
                $questionData['choice_b'] = $question['choices']['B'] ?? null;
                $questionData['choice_c'] = $question['choices']['C'] ?? null;
                $questionData['choice_d'] = $question['choices']['D'] ?? null;
            }

            // Insert or update the question
            DB::table('questions')->updateOrInsert(
                ['question_id' => $question['id']],
                $questionData
            );
        }
    }

    /**
     * Assign questions to a teacher assessment
     */
    private function assignQuestionsToAssessment($assessmentId, $questionIds)
    {
        $teacherId = Auth::guard('admin')->id();
        
        foreach ($questionIds as $index => $questionId) {
            $poolId = Str::uuid()->toString();
            
            DB::table('teacher_assessment_questions')->insert([
                'pool_id' => $poolId,
                'teacher_assessment_id' => $assessmentId,
                'question_id' => $questionId,
                'question_order' => $index + 1,
                'is_answered' => false,
                'is_current' => false,
                'created_by' => $teacherId,
                'added_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
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

        // Get quiz results if assessment is completed
        $quizResult = null;
        if ($assignment->status === 'Completed') {
            $quizResult = QuizResult::where('student_id', $student->id)
                ->where('assessment_id', $assessment->id)
                ->first();
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
        
        // Check if there's an active quiz session for a different assessment
        if (session('quiz_assessment_id') && session('quiz_assessment_id') != $assessment->id) {
            session()->forget(['quiz_assessment_id', 'quiz_questions', 'current_question_index', 'quiz_start_time', 'quiz_answers']);
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

        // Store quiz session data
        session([
            'quiz_assessment_id' => $assessment->id,
            'quiz_questions' => $questionDetails,
            'current_question_index' => 0,
            'quiz_start_time' => now(),
            'quiz_answers' => []
        ]);
        return view('student.assessments.quiz', compact('assessment', 'assignment', 'questionDetails'));
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
        if (session('quiz_assessment_id') != $assessment->id) {
            return response()->json(['success' => false, 'message' => 'Invalid quiz session.']);
        }

        $questions = session('quiz_questions', []);
        $currentIndex = session('current_question_index', 0);
        $answers = session('quiz_answers', []);

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

        // Store the answer
        $answers[] = [
            'question_id' => $request->question_id,
            'answer' => $request->answer,
            'is_correct' => $isCorrect,
            'time_taken' => $request->time_taken,
            'submitted_at' => now()
        ];

        // Update session
        session(['quiz_answers' => $answers]);
        
        // Debug logging for each answer submission
        \Log::info('Answer submitted', [
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'question_id' => $request->question_id,
            'answer' => $request->answer,
            'is_correct' => $isCorrect,
            'time_taken' => $request->time_taken,
            'total_answers_so_far' => count($answers)
        ]);

        // Move to next question
        $nextIndex = $currentIndex + 1;
        session(['current_question_index' => $nextIndex]);

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
        if (session('quiz_assessment_id') != $assessment->id) {
            return redirect()->route('teacher-assessments.show', $assessment->id)
                ->withErrors(['error' => 'Invalid quiz session.']);
        }

        // --- Debug: Initial session state ---
        \Log::info('Quiz completion started', [
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
        ]);

        $answers = session('quiz_answers', []);

        // Calculate total time
        $totalTime = collect($answers)->sum('time_taken') ?? 0;
        if ($totalTime === 0 && session('quiz_start_time')) {
            $totalTime = now()->diffInSeconds(session('quiz_start_time'));
        }

        // Calculate results
        $correctAnswers = collect($answers)->where('is_correct', true)->count();
        $totalQuestions = count($answers);
        $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;

        // --- Debug: Quiz result summary ---
        \Log::info('Quiz completion summary', [
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'score' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'percentage' => $percentage,
            'time_taken' => $totalTime,
        ]);

        // Store results
        QuizResult::updateOrCreate(
            [
                'student_id' => $student->id,
                'assessment_id' => $assessment->id,
            ],
            [
                'score' => $correctAnswers,
                'total_questions' => $totalQuestions,
                'percentage' => $percentage,
                'time_taken' => $totalTime,
                'answers' => $answers,
                'completed_at' => now(),
            ]
        );

        // --- Update assignment status (handles student or section assignments) ---
       // Get the student's section from their profile
        $studentProfile = DB::table('student_profile')
        ->where('user_id', $student->id)
        ->first();

        $studentSection = $studentProfile ? $studentProfile->section : null;

        // ✅ Update assignment status (handles both student-specific and section-based assignments)
        $assignment = AssessmentAssignment::where('assessment_id', $assessment->id)
        ->where(function ($query) use ($student, $studentSection) {
            $query->where('student_id', $student->id)
                  ->orWhere('section', $studentSection);
        })
        ->first();

        if ($assignment) {
            Log::info('Assignment found before update', [
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'student_section' => $studentSection,
                'assignment_section' => $assignment->section,
                'current_status' => $assignment->status
            ]);
        
            $assignment->update(['status' => 'Completed']);
        } else {
            Log::warning('Assignment not found for update', [
                'assessment_id' => $assessment->id,
                'student_id' => $student->id,
                'student_section' => $studentSection
            ]);
        }

        // Clear quiz session
        session()->forget([
            'quiz_assessment_id', 'quiz_questions', 'current_question_index',
            'quiz_start_time', 'quiz_answers'
        ]);

        // Redirect with success message
        return redirect()->route('teacher-assessments.show', $assessment->id)
            ->with('success', "Quiz completed! Score: {$correctAnswers}/{$totalQuestions} ({$percentage}%)");
    }

    /**
     * Get question details from JSON files or database
     */
    private function getQuestionDetails($questionId)
    {
        // Check if it's a custom question (stored in database)
        if (strpos($questionId, 'custom_') === 0) {
            $question = DB::table('questions')->where('question_id', $questionId)->first();
            if ($question) {
                return [
                    'question_id' => $question->question_id,
                    'question_text' => $question->question_text,
                    'question_type' => $question->question_type,
                    'choice_a' => $question->choice_a,
                    'choice_b' => $question->choice_b,
                    'choice_c' => $question->choice_c,
                    'choice_d' => $question->choice_d,
                    'correct_answer' => $question->correct_answer,
                    'hint_text' => $question->hint_text,
                    'explanation' => $question->explanation
                ];
            }
        }

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
