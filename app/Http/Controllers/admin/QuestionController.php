<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        $difficulty = $request->get('difficulty');
        $type = $request->get('type');
        $bloomsLevel = $request->get('blooms_level');
        $search = $request->get('search');

        $files = [
            'number_algebra' => [
                // DUMMY DATA
                // database_path('data/number_algebra/number_algebra_beginner.json'),
                // database_path('data/number_algebra/number_algebra_intermediate.json'),
                // database_path('data/number_algebra/number_algebra_advanced.json'),

                // EXPERT DATA
                database_path('expertdata/number_algebra/decimals_add_subtract/decimals_add_subtract_beginner.json'),
                database_path('expertdata/number_algebra/decimals_add_subtract/decimals_add_subtract_intermediate.json'),
                database_path('expertdata/number_algebra/decimals_add_subtract/decimals_add_subtract_advanced.json'),
                database_path('expertdata/number_algebra/decimals_word_problem/decimals_word_problem_beginner.json'),
                database_path('expertdata/number_algebra/decimals_word_problem/decimals_word_problem_intermediate.json'),
                database_path('expertdata/number_algebra/decimals_word_problem/decimals_word_problem_advanced.json'),
                database_path('expertdata/number_algebra/decimals_division_wordprob/decimals_division_wordprob_beginner.json'),
                database_path('expertdata/number_algebra/decimals_division_wordprob/decimals_division_wordprob_intermediate.json'),
                database_path('expertdata/number_algebra/decimals_division_wordprob/decimals_division_wordprob_advanced.json'),          
                database_path('expertdata/number_algebra/decimals_division/decimals_division_beginner.json'),
                database_path('expertdata/number_algebra/decimals_division/decimals_division_intermediate.json'),
                database_path('expertdata/number_algebra/decimals_division/decimals_division_advanced.json'),
                database_path('expertdata/number_algebra/fraction_division/fraction_division_beginner.json'),
                database_path('expertdata/number_algebra/fraction_division/fraction_division_intermediate.json'),
                database_path('expertdata/number_algebra/fraction_division/fraction_division_advanced.json'),
                database_path('expertdata/number_algebra/fraction_division_wordprob/fraction_division_wordprob_beginner.json'),
                database_path('expertdata/number_algebra/fraction_division_wordprob/fraction_division_wordprob_intermediate.json'),
                database_path('expertdata/number_algebra/fraction_division_wordprob/fraction_division_wordprob_advanced.json'),
                database_path('expertdata/number_algebra/fraction_multiplication/fraction_multiplication_beginner.json'),
                database_path('expertdata/number_algebra/fraction_multiplication/fraction_multiplication_intermediate.json'),
                database_path('expertdata/number_algebra/fraction_multiplication/fraction_multiplication_advanced.json'),
                database_path('expertdata/number_algebra/gcf_lcm/gcf_lcm_beginner.json'),
                database_path('expertdata/number_algebra/gcf_lcm/gcf_lcm_intermediate.json'),
                database_path('expertdata/number_algebra/gcf_lcm/gcf_lcm_advanced.json'),
                database_path('expertdata/number_algebra/decimals_multiplication_wordprob/decimals_multiplication_wordprob_beginner.json'),
                database_path('expertdata/number_algebra/decimals_multiplication_wordprob/decimals_multiplication_wordprob_intermediate.json'),
                database_path('expertdata/number_algebra/decimals_multiplication_wordprob/decimals_multiplication_wordprob_advanced.json'),
                database_path('expertdata/number_algebra/fraction_multiplication_wordprob/fraction_multiplication_wordprob_beginner.json'),
                database_path('expertdata/number_algebra/fraction_multiplication_wordprob/fraction_multiplication_wordprob_intermediate.json'),
                database_path('expertdata/number_algebra/fraction_multiplication_wordprob/fraction_multiplication_wordprob_advanced.json'),
                database_path('expertdata/number_algebra/order_operations/order_operations_beginner.json'),
                database_path('expertdata/number_algebra/order_operations/order_operations_intermediate.json'),
                database_path('expertdata/number_algebra/order_operations/order_operations_advanced.json'),
                database_path('expertdata/number_algebra/percentage/percentage_beginner.json'),
                database_path('expertdata/number_algebra/percentage/percentage_intermediate.json'),
                database_path('expertdata/number_algebra/percentage/percentage_advanced.json'),
                ],
            'measurement_geometry' => [
                // DUMMY DATA
                // database_path('data/measurement_geometry/measurement_geometry_beginner.json'),
                // database_path('data/measurement_geometry/measurement_geometry_intermediate.json'),
                // database_path('data/measurement_geometry/measurement_geometry_advanced.json'),

                // EXPERT DATA
                 database_path('expertdata/measurement_geometry/area_perimeter/area_perimeter_beginner.json'),
                 database_path('expertdata/measurement_geometry/area_perimeter/area_perimeter_intermediate.json'),
                 database_path('expertdata/measurement_geometry/area_perimeter/area_perimeter_advanced.json'),
            ],
            'data_probability' => [
                // DUMMY DATA
                // database_path('data/data_probability/data_probability_beginner.json'),
                // database_path('data/data_probability/data_probability_intermediate.json'),
                // database_path('data/data_probability/data_probability_advanced.json'),

                // EXPERT DATA
            ],
        ];

        $all = [];
        
        // Only load files for the selected category if filtered, otherwise load all
        $categoriesToLoad = $category ? [$category] : array_keys($files);
        
        foreach ($categoriesToLoad as $cat) {
            if (!isset($files[$cat])) {
                continue;
            }
            
            // Only load files for the selected difficulty if filtered
            $filesToLoad = $files[$cat];
            if ($difficulty) {
                $difficultyIndex = [
                    'beginner' => 0,
                    'intermediate' => 1,
                    'advanced' => 2
                ];
                if (isset($difficultyIndex[$difficulty], $files[$cat][$difficultyIndex[$difficulty]])) {
                    $filesToLoad = [$files[$cat][$difficultyIndex[$difficulty]]];
                } else {
                    $filesToLoad = [];
                }
            }
            
            foreach ($filesToLoad as $path) {
                if (!file_exists($path)) {
                    continue;
                }
                
                $jsonContent = file_get_contents($path);
                $data = json_decode($jsonContent, true);
                
                // Check if JSON is valid
                if (json_last_error() !== JSON_ERROR_NONE) {
                    \Log::error("JSON decode error in {$path}: " . json_last_error_msg());
                    continue;
                }
                
                // Handle different JSON structures
                if (isset($data['questions']) && is_array($data['questions'])) {
                    $questions = $data['questions'];
                } elseif (is_array($data)) {
                    $questions = $data;
                } else {
                    continue;
                }
                
                foreach ($questions as $q) {
                    if (!is_array($q)) {
                        continue;
                    }
                    
                    $q['competency'] = Str::of($q['competency'] ?? $cat)->snake()->lower()->value();
                    $q['difficulty_level'] = Str::of($q['difficulty_level'] ?? 'beginner')->snake()->lower()->value();
                    $q['question_type'] = Str::of($q['question_type'] ?? 'multiple_choice')->snake()->lower()->value();
                    $q['blooms_level'] = Str::of($q['blooms_level'] ?? ($q['blooms_taxonomy'] ?? 'remember'))->snake()->lower()->value();
                    $q['question_source'] = 'built_in';

                    $all[] = $q;
                }
            }
        }

         // Enrich built-in (JSON) questions with DB values when available
         if (!empty($all)) {
            $jsonIds = array_values(array_filter(array_map(function ($q) {
                return $q['question_id'] ?? null;
            }, $all)));
            if (!empty($jsonIds)) {
                $overrides = Question::whereIn('question_id', $jsonIds)->get()->keyBy('question_id');
                foreach ($all as &$q) {
                    $qid = $q['question_id'] ?? null;
                    if ($qid && isset($overrides[$qid])) {
                        $dbq = $overrides[$qid]->toArray();
                        foreach ([
                            'base_points',
                            'max_allowed_time',
                            'competency',
                            'estimated_difficulty_weight',
                            'topic_tag',
                            'question_text',
                            'choice_a', 'choice_b', 'choice_c', 'choice_d',
                            'correct_answer',
                            'blooms_taxonomy',
                        ] as $field) {
                            if (array_key_exists($field, $dbq) && $dbq[$field] !== null) {
                                $q[$field] = $dbq[$field];
                            }
                        }

                        if (!empty($dbq['blooms_taxonomy'])) {
                            $q['blooms_level'] = Str::of($dbq['blooms_taxonomy'])->snake()->lower()->value();
                        }
                    }
                }
                unset($q);
            }
        }

        // Include custom (DB) questions ONLY (not overrides of built-in)
        $dbQuery = Question::where('question_source', 'custom');
        if ($category) {
            $dbQuery->where('competency', $category);
        }
        if ($difficulty) {
            $dbQuery->where('difficulty_level', $difficulty);
        }
        if ($type) {
            $dbQuery->where('question_type', $type);
        }
        if ($bloomsLevel) {
            $dbQuery->where('blooms_taxonomy', $bloomsLevel);
        }
        $db = $dbQuery->get()->toArray();

        $combined = array_merge($all, $db);

        // Apply type filter only (category and difficulty already filtered above)
        if ($type) {
            $combined = array_values(array_filter($combined, function ($q) use ($type) {
                return ($q['question_type'] ?? null) === $type;
            }));
        }
        if ($bloomsLevel) {
            $combined = array_values(array_filter($combined, function ($q) use ($bloomsLevel) {
                $questionBlooms = Str::of($q['blooms_level'] ?? ($q['blooms_taxonomy'] ?? ''))->snake()->lower()->value();
                return $questionBlooms === $bloomsLevel;
            }));
        }

        // Apply search filter
        if ($search) {
            $combined = array_values(array_filter($combined, function ($q) use ($search) {
                return stripos($q['question_text'] ?? '', $search) !== false ||
                       stripos($q['question_id'] ?? '', $search) !== false ||
                       stripos($q['topic_tag'] ?? '', $search) !== false;
            }));
        }

        // Convert to collection for pagination
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedData = array_slice($combined, $offset, $perPage);
        $total = count($combined);

        $questions = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedData,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Calculate next question number for auto-generation
        $nextQuestionNumber = $this->getNextQuestionNumber($category, $difficulty);

        return view('admin.admin.management.questions-management', compact('questions', 'nextQuestionNumber'));
    }

    public function data(Request $request)
    {
        $category = $request->get('category');
        $difficulty = $request->get('difficulty');
        $type = $request->get('type');
        $bloomsLevel = $request->get('blooms_level');

        $files = [
            'number_algebra' => [
                database_path('expertdata/number_algebra/decimals_add_subtract/decimals_add_subtract_beginner.json'),
            ],
            'measurement_geometry' => [
                database_path('data/measurement_geometry/measurement_geometry_beginner.json'),
                database_path('data/measurement_geometry/measurement_geometry_intermediate.json'),
                database_path('data/measurement_geometry/measurement_geometry_advanced.json'),
            ],
            'data_probability' => [
                database_path('data/data_probability/data_probability_beginner.json'),
                database_path('data/data_probability/data_probability_intermediate.json'),
                database_path('data/data_probability/data_probability_advanced.json'),
            ],
        ];

        $all = [];
        $categoriesToLoad = $category ? [$category] : array_keys($files);
        foreach ($categoriesToLoad as $cat) {
            if (!isset($files[$cat])) {
                continue;
            }
            foreach ($files[$cat] as $path) {
                if (!file_exists($path)) {
                    continue;
                }
                
                $jsonContent = file_get_contents($path);
                $data = json_decode($jsonContent, true);
                
                // Check if JSON is valid
                if (json_last_error() !== JSON_ERROR_NONE) {
                    continue;
                }
                
                // Handle different JSON structures
                if (isset($data['questions']) && is_array($data['questions'])) {
                    $questions = $data['questions'];
                } elseif (is_array($data)) {
                    $questions = $data;
                } else {
                    continue;
                }
                
                foreach ($questions as $q) {
                    if (!is_array($q)) {
                        continue;
                    }
                    
                    $q['competency'] = Str::of($q['competency'] ?? $cat)->snake()->lower()->value();
                    $q['difficulty_level'] = Str::of($q['difficulty_level'] ?? 'beginner')->snake()->lower()->value();
                    $q['question_type'] = Str::of($q['question_type'] ?? 'multiple_choice')->snake()->lower()->value();
                    $q['blooms_level'] = Str::of($q['blooms_level'] ?? ($q['blooms_taxonomy'] ?? 'remember'))->snake()->lower()->value();
                    $q['question_source'] = 'built_in';
                    
                    
                    $all[] = $q;
                }
            }
        }

           // Enrich built-in (JSON) questions with DB values when available
           if (!empty($all)) {
            $jsonIds = array_values(array_filter(array_map(function ($q) {
                return $q['question_id'] ?? null;
            }, $all)));
            if (!empty($jsonIds)) {
                $overrides = Question::whereIn('question_id', $jsonIds)->get()->keyBy('question_id');
                foreach ($all as &$q) {
                    $qid = $q['question_id'] ?? null;
                    if ($qid && isset($overrides[$qid])) {
                        $dbq = $overrides[$qid]->toArray();
                        foreach ([
                            'base_points',
                            'max_allowed_time',
                            'estimated_difficulty_weight',
                            'competency',
                            'topic_tag',
                            'question_text',
                            'choice_a', 'choice_b', 'choice_c', 'choice_d',
                            'correct_answer',
                            'blooms_taxonomy',
                        ] as $field) {
                            if (array_key_exists($field, $dbq) && $dbq[$field] !== null) {
                                $q[$field] = $dbq[$field];
                            }
                        }

                        if (!empty($dbq['blooms_taxonomy'])) {
                            $q['blooms_level'] = Str::of($dbq['blooms_taxonomy'])->snake()->lower()->value();
                        }
                    }
                }
                unset($q);
            }
        }

        // Include custom (DB) questions ONLY (not overrides of built-in)
        $dbQuery = Question::where('question_source', 'custom');
        if ($category) {
            $dbQuery->where('competency', $category);
        }
        if ($difficulty) {
            $dbQuery->where('difficulty_level', $difficulty);
        }
        if ($type) {
            $dbQuery->where('question_type', $type);
        }
        if ($bloomsLevel) {
            $dbQuery->where('blooms_taxonomy', $bloomsLevel);
        }
        $db = $dbQuery->get()->toArray();

        $combined = array_merge($all, $db);

        // Apply filters if provided (covers JSON-sourced questions)
        if ($difficulty) {
            $combined = array_values(array_filter($combined, function ($q) use ($difficulty) {
                return ($q['difficulty_level'] ?? null) === $difficulty;
            }));
        }
        if ($type) {
            $combined = array_values(array_filter($combined, function ($q) use ($type) {
                return ($q['question_type'] ?? null) === $type;
            }));
        }
        if ($bloomsLevel) {
            $combined = array_values(array_filter($combined, function ($q) use ($bloomsLevel) {
                $questionBlooms = Str::of($q['blooms_level'] ?? ($q['blooms_taxonomy'] ?? ''))->snake()->lower()->value();
                return $questionBlooms === $bloomsLevel;
            }));
        }

        return response()->json([
            'success' => true,
            'data' => $combined,
            'count' => count($combined),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_id' => 'required|string|max:50|unique:questions,question_id',
            'competency' => 'required|in:number_algebra,measurement_geometry,data_probability',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'topic_tag' => 'required|string|max:100',
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,fill_blanks,true_false,drag_drop,connect_dots',
            'choice_a' => 'nullable|string',
            'choice_b' => 'nullable|string',
            'choice_c' => 'nullable|string',
            'choice_d' => 'nullable|string',
            'correct_answer' => 'required|string',
            'hint_text' => 'nullable|string',
            'explanation' => 'nullable|string',
            'max_allowed_time' => 'required|integer|min:5',
            'estimated_difficulty_weight' => 'nullable|numeric|min:0|max:5',
            'base_points' => 'required|integer|min:1',
        ]);

        $validated['question_source'] = 'custom';
        $validated['is_active'] = true;

        $question = Question::create($validated);
        
        // Log notification
        $this->logNotification('question_management', 'Created new question', [
            'question_id' => $validated['question_id'],
            'competency' => $validated['competency'],
            'difficulty_level' => $validated['difficulty_level'],
            'question_type' => $validated['question_type'],
            'topic_tag' => $validated['topic_tag'],
            'base_points' => $validated['base_points']
        ]);
        
        return redirect()->route('admin.management.questions')
            ->with('success', 'Question created successfully!');
    }

    public function update(Request $request, string $questionId)
    {
        // Try to find existing question in database
        $question = Question::where('question_id', $questionId)->first();
        
        $validated = $request->validate([
            'competency' => 'sometimes|in:number_algebra,measurement_geometry,data_probability',
            'difficulty_level' => 'sometimes|in:beginner,intermediate,advanced',
            'topic_tag' => 'sometimes|string|max:100',
            'question_text' => 'sometimes|string',
            'question_type' => 'sometimes|in:multiple_choice,fill_blanks,true_false,drag_drop,connect_dots',
            'choice_a' => 'nullable|string',
            'choice_b' => 'nullable|string',
            'choice_c' => 'nullable|string',
            'choice_d' => 'nullable|string',
            'correct_answer' => 'sometimes|string',
            'hint_text' => 'nullable|string',
            'explanation' => 'nullable|string',
            'max_allowed_time' => 'sometimes|integer|min:5',
            'estimated_difficulty_weight' => 'nullable|numeric|min:0|max:5',
            'base_points' => 'sometimes|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($question) {
            // Update existing question
            $question->fill($validated)->save();
            $actionType = 'updated';
        } else {
            // Create new override for built-in question
            $validated['question_id'] = $questionId;
            $validated['question_source'] = 'built_in'; // Mark as override of built-in
            $validated['is_active'] = true;
            Question::create($validated);
            $actionType = 'created override for';
        }
        
        // Log notification
        $this->logNotification('question_management', "Question {$actionType}", [
            'question_id' => $questionId,
            'competency' => $validated['competency'] ?? 'unchanged',
            'difficulty_level' => $validated['difficulty_level'] ?? 'unchanged',
            'question_type' => $validated['question_type'] ?? 'unchanged',
            'action_type' => $actionType,
            'fields_updated' => array_keys($validated)
        ]);
        
        return redirect()->route('admin.management.questions')
            ->with('success', 'Question updated successfully!');
    }

    public function destroy(string $questionId)
    {
        $question = Question::where('question_id', $questionId)->firstOrFail();
        
        // Store question info before deletion for notification
        $questionInfo = [
            'question_id' => $question->question_id,
            'competency' => $question->competency,
            'difficulty_level' => $question->difficulty_level,
            'question_type' => $question->question_type,
            'topic_tag' => $question->topic_tag,
            'question_source' => $question->question_source
        ];
        
        $question->delete();
        
        // Log notification after successful deletion
        $this->logNotification('question_management', 'Deleted question', $questionInfo);
        
        return redirect()->route('admin.management.questions')
            ->with('success', 'Question deleted successfully!');
    }

    private function getNextQuestionNumber($category = null, $difficulty = null)
    {
        // Get all custom questions
        $query = Question::query();
        
        if ($category && $difficulty) {
            $categoryPrefix = $this->getCategoryPrefix($category);
            $difficultyPrefix = $this->getDifficultyPrefix($difficulty);
            $prefix = "{$categoryPrefix}-{$difficultyPrefix}";
            
            $lastQuestion = $query->where('question_id', 'like', "{$prefix}-%")
                ->orderBy('question_id', 'desc')
                ->first();
            
            if ($lastQuestion) {
                // Extract number from question_id (e.g., "NA-B-005" -> 5)
                preg_match('/\d+$/', $lastQuestion->question_id, $matches);
                return isset($matches[0]) ? intval($matches[0]) + 1 : 1;
            }
        }
        
        // Default: get highest number across all questions
        $allQuestions = Question::orderBy('question_id', 'desc')->get();
        $maxNumber = 0;
        
        foreach ($allQuestions as $q) {
            preg_match('/\d+$/', $q->question_id, $matches);
            if (isset($matches[0])) {
                $maxNumber = max($maxNumber, intval($matches[0]));
            }
        }
        
        return $maxNumber + 1;
    }

    private function getCategoryPrefix($category)
    {
        $prefixes = [
            'number_algebra' => 'NA',
            'measurement_geometry' => 'MG',
            'data_probability' => 'DP'
        ];
        return $prefixes[$category] ?? 'NA';
    }

    private function getDifficultyPrefix($difficulty)
    {
        $prefixes = [
            'beginner' => 'B',
            'intermediate' => 'I',
            'advanced' => 'A'
        ];
        return $prefixes[$difficulty] ?? 'B';
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
                \DB::table('admin_notifications')->insert([
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