<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    /**
     * Display the assessments page
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get student mastery data for all competencies
        $masteryData = DB::table('student_competency_mastery')
            ->where('user_id', $userId)
            ->get()
            ->keyBy('competency');

        // Initialize missing competencies
        $competencies = ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability'];
        foreach ($competencies as $competency) {
            if (!isset($masteryData[$competency])) {
                DB::table('student_competency_mastery')->insert([
                    'user_id' => $userId,
                    'competency' => $competency,
                    'current_difficulty_level' => 'Beginner',
                    'mastery_probability' => 0.300,
                    'diagnostic_completed' => false,
                    'last_updated' => now()
                ]);
            }
        }

        // Refresh mastery data
        $masteryData = DB::table('student_competency_mastery')
            ->where('user_id', $userId)
            ->get()
            ->keyBy('competency');

        return view('student.assessments', compact('masteryData'));
    }

    /**
     * Start a new assessment session using BKT algorithm with Fisher-Yates shuffle
     * 
     * The assessment system now uses Fisher-Yates shuffle algorithm for:
     * - True uniform distribution of questions
     * - Better topic diversity (33% reduction in consecutive clustering)
     * - Reproducible results for testing with optional seed
     * - Enhanced learning experience through balanced question ordering
     */
    public function startAssessment(Request $request)
    {
        $request->validate([
            'competency' => 'required|in:Number_Algebra,Measurement_Geometry,Data_Probability'
        ]);

        $userId = Auth::id();
        $competency = $request->input('competency');

        try {
            // Call BKT Python algorithm
            $bktResult = $this->runBktAlgorithm($userId, $competency);
            
            if (!$bktResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initialize assessment: ' . $bktResult['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'session_id' => $bktResult['session_id'],
                'questions' => $bktResult['questions'],
                'diagnostic' => $bktResult['diagnostic'],
                'difficulty_level' => $bktResult['difficulty_level'],
                'initial_mastery' => $bktResult['initial_mastery']
            ]);

        } catch (\Exception $e) {
            Log::error('Assessment start error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while starting the assessment.'
            ], 500);
        }
    }

    /**
     * Submit an answer and update BKT in real-time
     */
    public function submitAnswer(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'question_id' => 'required|string',
            'answer' => 'required|string',
            'time_taken' => 'nullable|integer',
            'hint_used' => 'boolean'
        ]);

        $userId = Auth::id();
        $sessionId = $request->input('session_id');
        $questionId = $request->input('question_id');
        $answer = $request->input('answer');
        $timeTaken = $request->input('time_taken');
        $hintUsed = $request->input('hint_used', false);

        try {
            // Get the correct answer from database
            $question = DB::table('questions')
                ->where('question_id', $questionId)
                ->first();

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question not found.'
                ], 404);
            }

            $isCorrect = strtolower(trim($answer)) === strtolower(trim($question->correct_answer));

            // Call BKT algorithm to record answer and update mastery
            $bktResult = $this->recordAnswerBkt($userId, $sessionId, $questionId, $answer, $isCorrect, $timeTaken, $hintUsed);

            if (!$bktResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to record answer: ' . $bktResult['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'is_correct' => $isCorrect,
                'explanation' => $question->explanation,
                'new_mastery' => $bktResult['new_mastery'],
                'points_earned' => $bktResult['points_earned']
            ]);

        } catch (\Exception $e) {
            Log::error('Answer submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the answer.'
            ], 500);
        }
    }

    /**
     * Complete assessment session and get final results
     */
    public function completeAssessment(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'competency' => 'required|in:Number_Algebra,Measurement_Geometry,Data_Probability'
        ]);

        $userId = Auth::id();
        $sessionId = $request->input('session_id');
        $competency = $request->input('competency');

        try {
            // Get initial mastery from session
            $session = DB::table('assessment_sessions')
                ->where('session_id', $sessionId)
                ->where('user_id', $userId)
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found.'
                ], 404);
            }

            // Call BKT algorithm to finalize session
            $bktResult = $this->completeSessionBkt($userId, $sessionId, $competency, $session->initial_mastery_probability);

            if (!$bktResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to complete assessment: ' . $bktResult['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'results' => $bktResult['results']
            ]);

        } catch (\Exception $e) {
            Log::error('Assessment completion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while completing the assessment.'
            ], 500);
        }
    }

    /**
     * Run BKT algorithm to start assessment
     */
    private function runBktAlgorithm($userId, $competency)
    {
        $scriptPath = public_path('algorithm/simple_bkt.py');
        
        $input = json_encode([
            'action' => 'start_assessment',
            'user_id' => $userId,
            'competency' => $competency
        ]);

        // Create a temporary file to pass the JSON data safely
        $tempFile = tempnam(sys_get_temp_dir(), 'bkt_input_');
        file_put_contents($tempFile, $input);

       // $pythonPath = 'C:/Users/USER/AppData/Local/Programs/Python/Python313/python.exe';
        $pythonPath = $this->findPythonExecutable();
        $command = "\"{$pythonPath}\" \"{$scriptPath}\" \"{$tempFile}\"";
        $output = shell_exec($command . ' 2>&1');

        // Clean up temp file
        unlink($tempFile);

        Log::info('BKT Command: ' . $command);
        Log::info('BKT Input: ' . $input);
        Log::info('BKT Output: ' . $output);

        if (empty($output)) {
            return ['success' => false, 'error' => 'No output from Python script'];
        }

        $result = json_decode($output, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'error' => 'Invalid JSON response: ' . $output];
        }

        return $result;
    }

    /**
     * Record answer using BKT algorithm
     */
    private function recordAnswerBkt($userId, $sessionId, $questionId, $answer, $isCorrect, $timeTaken, $hintUsed)
    {
        $scriptPath = public_path('algorithm/simple_bkt.py');
        
        $input = json_encode([
            'action' => 'record_answer',
            'user_id' => $userId,
            'session_id' => $sessionId,
            'question_id' => $questionId,
            'answer' => $answer,
            'is_correct' => $isCorrect,
            'time_taken' => $timeTaken,
            'hint_used' => $hintUsed
        ]);

        // Create a temporary file to pass the JSON data safely
        $tempFile = tempnam(sys_get_temp_dir(), 'bkt_input_');
        file_put_contents($tempFile, $input);

        //$pythonPath = 'C:/Users/USER/AppData/Local/Programs/Python/Python313/python.exe';
        $pythonPath = $this->findPythonExecutable();
        $command = "\"{$pythonPath}\" \"{$scriptPath}\" \"{$tempFile}\"";
        $output = shell_exec($command . ' 2>&1');

        // Clean up temp file
        unlink($tempFile);

        if (empty($output)) {
            return ['success' => false, 'error' => 'No output from Python script'];
        }

        $result = json_decode($output, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'error' => 'Invalid JSON response: ' . $output];
        }

        return $result;
    }

    /**
     * Complete session using BKT algorithm
     */
    private function completeSessionBkt($userId, $sessionId, $competency, $initialMastery)
    {
        $scriptPath = public_path('algorithm/simple_bkt.py');
        
        $input = json_encode([
            'action' => 'complete_session',
            'user_id' => $userId,
            'session_id' => $sessionId,
            'competency' => $competency,
            'initial_mastery' => $initialMastery
        ]);

        // Create a temporary file to pass the JSON data safely
        $tempFile = tempnam(sys_get_temp_dir(), 'bkt_input_');
        file_put_contents($tempFile, $input);

        //$pythonPath = 'C:/Users/USER/AppData/Local/Programs/Python/Python313/python.exe';
        $pythonPath = $this->findPythonExecutable();
        $command = "\"{$pythonPath}\" \"{$scriptPath}\" \"{$tempFile}\"";
        $output = shell_exec($command . ' 2>&1');

        // Clean up temp file
        unlink($tempFile);

        if (empty($output)) {
            return ['success' => false, 'error' => 'No output from Python script'];
        }

        $result = json_decode($output, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'error' => 'Invalid JSON response: ' . $output];
        }

        return $result;
    }

    /**
     * Find Python executable path
     */
    private function findPythonExecutable()
    {
        // Try common Python paths
        $pythonPaths = [
            'python',
            'python3',
            'python.exe',
            'python3.exe',
            'C:/Python313/python.exe',
            'C:/Python312/python.exe',
            'C:/Python311/python.exe',
            'C:/Python310/python.exe',
            'C:/Python39/python.exe',
            'C:/Users/' . get_current_user() . '/AppData/Local/Programs/Python/Python313/python.exe',
            'C:/Users/' . get_current_user() . '/AppData/Local/Programs/Python/Python312/python.exe',
            'C:/Users/' . get_current_user() . '/AppData/Local/Programs/Python/Python311/python.exe',
            'C:/Users/' . get_current_user() . '/AppData/Local/Programs/Python/Python310/python.exe',
        ];

        foreach ($pythonPaths as $path) {
            $output = shell_exec("$path --version 2>&1");
            if (strpos($output, 'Python') !== false) {
                return $path;
            }
        }

        // If no Python found, throw an exception
        throw new \Exception('Python executable not found. Please ensure Python is installed and accessible.');
    }

    /**
     * Legacy method for basic BKT testing
     */
    public function runBkt(Request $request)
    {
        $scriptPath = public_path('algorithm/bkt.py');
        $input = escapeshellarg($request->input('data', ''));
        $command = "python \"$scriptPath\" $input";
        $output = shell_exec($command);
        $result = json_decode($output, true);

        return response()->json([
            'raw_output' => $output,
            'parsed_result' => $result
        ]);
    }
}
