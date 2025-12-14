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
    public function index(Request $request)
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
        
        // Get filter parameters
        $statusFilter = $request->input('status');
        $categoryFilter = $request->input('category');
        $difficultyFilter = $request->input('difficulty');
        
        // Build query for assessments created by this teacher
        $query = Assessment::where('created_by', $teacher->id)
            ->with(['assignments.student.studentProfile']);
        
        // Apply status filter (default: exclude archived)
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        } else {
            // By default, exclude archived assessments
            $query->where('status', '!=', 'Archived');
        }
        
        // Apply category filter
        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }
        
        // Apply difficulty filter
        if ($difficultyFilter) {
            $query->where('difficulty', $difficultyFilter);
        }
        
        $assessments = $query->orderBy('created_at', 'desc')->get();
        
        return view('admin.teacher.assessments.index', compact(
            'assessments', 
            'teacherSections', 
            'studentsData',
            'statusFilter',
            'categoryFilter',
            'difficultyFilter'
        ));
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
            'status' => 'Draft' // Will be updated to Active if assignments are created
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

        // Track if any assignments were created
        $assignmentsCreated = false;

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
            $assignmentsCreated = true;
        
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
                
                if ($createdCount > 0) {
                    $assignmentsCreated = true;
                }
        
            }
            
        } else {
            \Log::warning('No assignment conditions met', [
                'has_selectedStudents' => !empty($selectedStudents),
                'has_sections' => $request->has('sections'),
                'assessment_id' => $assessment->id
            ]);
        }

        // Update status to Active if assignments were created
        if ($assignmentsCreated) {
            $assessment->status = 'Active';
            $assessment->save();
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

    /**
     * Show results for a teacher assessment (Grouped by Student)
     */
    public function results(Assessment $assessment)
    {
        $teacher = Auth::guard('admin')->user();
        
        // Verify teacher owns this assessment
        if ($assessment->created_by !== $teacher->id) {
            abort(403, 'You do not have permission to view these results.');
        }

        // Get all sessions for this assessment with student details
        $allSessions = TeacherAssessmentSession::where('teacher_assessment_id', $assessment->id)
            ->with(['user.studentProfile'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by user_id to display one row per student
        $students = $allSessions->groupBy('user_id')->map(function ($sessions, $userId) {
            $user = $sessions->first()->user;
            $studentProfile = $user ? $user->studentProfile : null;
            
            // Calculate stats - Use lowercase 'completed' as stored in DB
            $completedSessions = $sessions->where('status', 'completed');
            
            // Find the session with the highest accuracy
            $bestSession = $completedSessions->sortByDesc('accuracy_percentage')->first();
            $highestScorePercent = $bestSession ? $bestSession->accuracy_percentage : 0;
            
            // Format highest score as "Correct/Total" strings
            $highestScoreRaw = $bestSession 
                ? $bestSession->correct_answers . '/' . $bestSession->total_questions 
                : '-';

            $latestSession = $sessions->sortByDesc('created_at')->first();
            
            return (object) [
                'user_id' => $userId,
                'user' => $user,
                'profile' => $studentProfile,
                'attempts_count' => $sessions->count(),
                'completed_count' => $completedSessions->count(),
                'highest_score' => $highestScorePercent,
                'highest_score_raw' => $highestScoreRaw,
                'latest_session' => $latestSession
            ];
        });

        return view('admin.teacher.assessments.results', compact('assessment', 'students'));
    }

    /**
     * Get attempts for a specific student and assessment (AJAX)
     */
    public function getStudentAttempts(Assessment $assessment, User $student)
    {
        $sessions = TeacherAssessmentSession::where('teacher_assessment_id', $assessment->id)
            ->where('user_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Format for JSON response
        $attempts = $sessions->map(function($session) {
            $duration = '-';
            if ($session->completed_at && $session->started_at) {
                $diff = $session->started_at->diff($session->completed_at);
                $duration = sprintf('%02d:%02d', $diff->i + ($diff->h * 60), $diff->s);
            }
            
            return [
                'session_id' => $session->session_id,
                'date_taken' => $session->created_at->format('M d, Y • h:i A'),
                'score' => $session->correct_answers . '/' . $session->total_questions, // Showing correct count vs total questions
                'questions_total' => $session->total_questions, // Using question count as max score reference
                'percentage' => number_format($session->accuracy_percentage, 2),
                'status' => ucfirst($session->status),
                'duration' => $duration,
                'action_url' => route('teacher.assessments.review-session', $session->session_id)
            ];
        });
        
        return response()->json(['attempts' => $attempts]);
    }

    /**
     * Review a specific assessment session
     */
    public function reviewSession(TeacherAssessmentSession $session)
    {
        $teacher = Auth::guard('admin')->user();
        
        // Verify teacher owns this assessment
        if ($session->assessment->created_by !== $teacher->id) {
            abort(403, 'You do not have permission to view this result.');
        }
        
        // Load relationships
        $session->load(['responses', 'user.studentProfile', 'assessment']);
        
        return view('admin.teacher.assessments.review', compact('session'));
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

    /**
     * Archive an assessment
     */
    public function archive(Assessment $assessment)
    {
        $teacher = Auth::guard('admin')->user();
        
        // Verify teacher owns this assessment
        if ($assessment->created_by !== $teacher->id) {
            return redirect()->route('teacher.assessments')->withErrors(['error' => 'Unauthorized']);
        }
        
        $assessment->status = 'Archived';
        $assessment->save();
        
        return redirect()->route('teacher.assessments')->with('success', 'Assessment archived successfully!');
    }

    /**
     * Unarchive an assessment
     */
    public function unarchive(Assessment $assessment)
    {
        $teacher = Auth::guard('admin')->user();
        
        // Verify teacher owns this assessment
        if ($assessment->created_by !== $teacher->id) {
            return redirect()->route('teacher.assessments')->withErrors(['error' => 'Unauthorized']);
        }
        
        $assessment->status = 'Active';
        $assessment->save();
        
        return redirect()->route('teacher.assessments')->with('success', 'Assessment unarchived successfully!');
    }
}
