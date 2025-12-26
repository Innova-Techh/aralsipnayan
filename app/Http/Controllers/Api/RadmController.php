<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RadmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RadmController extends Controller
{
    protected $radmService;

    public function __construct(RadmService $radmService)
    {
        $this->radmService = $radmService;
    }

    /**
     * Evaluate a window of responses for RADM detection
     * 
     * POST /api/radm/evaluate
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function evaluate(Request $request)
    {
        \Log::info('[RADM API] Evaluate request received', [
            'data' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $validator = Validator::make($request->all(), [
            'sessionId' => 'required|string',
            'studentId' => 'required|integer',
            'difficultyLevel' => 'required|in:beginner,intermediate,advanced',
            'responses' => 'required|array|size:5',
            'responses.*.question_id' => 'required',
            'responses.*.time_taken' => 'required|numeric|min:0',
            'responses.*.is_correct' => 'required|boolean',
            'responses.*.bkt_probability' => 'nullable|numeric|min:0|max:1',
        ]);

        if ($validator->fails()) {
            \Log::warning('[RADM API] Validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Note: In a real implementation, you'd verify the session and student
            // against the authenticated user to prevent tampering

            $sessionId = $request->input('sessionId');
            $studentId = $request->input('studentId');
            $difficultyLevel = $request->input('difficultyLevel');

            \Log::info('[RADM API] Calling evaluateSession', [
                'session_id' => $sessionId,
                'student_id' => $studentId,
                'difficulty_level' => $difficultyLevel
            ]);

            // Evaluate using the service
            $detection = $this->radmService->evaluateSession(
                $sessionId,
                $studentId,
                $difficultyLevel
            );

            if (!$detection) {
                \Log::info('[RADM API] No detection created (not enough responses or not at boundary)');
                return response()->json([
                    'success' => true,
                    'message' => 'Not enough responses to evaluate',
                    'data' => null
                ]);
            }

            \Log::info('[RADM API] Detection created successfully', [
                'detection_id' => $detection->id,
                'intervention_triggered' => $detection->intervention_triggered
            ]);

            return response()->json([
                'success' => true,
                'message' => 'RADM evaluation complete',
                'data' => [
                    'detection_id' => $detection->id,
                    'rai_score' => $detection->rai_score,
                    'intervention_triggered' => $detection->intervention_triggered,
                    'time_flag' => $detection->time_flag,
                    'accuracy_flag' => $detection->accuracy_flag,
                    'bkt_flag' => $detection->bkt_flag,
                    'avg_response_time' => $detection->avg_response_time,
                    'accuracy' => $detection->accuracy,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('RADM evaluation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during evaluation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Acknowledge an intervention
     * 
     * POST /api/radm/acknowledge/{detectionId}
     * 
     * @param int $detectionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function acknowledge($detectionId)
    {
        try {
            $this->radmService->acknowledgeIntervention($detectionId);

            return response()->json([
                'success' => true,
                'message' => 'Intervention acknowledged'
            ]);

        } catch (\Exception $e) {
            \Log::error('RADM acknowledgment error', [
                'detection_id' => $detectionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to acknowledge intervention'
            ], 500);
        }
    }

    /**
     * Check for unacknowledged interventions
     * 
     * GET /api/radm/check-intervention
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkIntervention(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sessionId' => 'required|string',
            'studentId' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sessionId = $request->input('sessionId');
            $studentId = $request->input('studentId');

            $hasIntervention = $this->radmService->hasUnacknowledgedIntervention(
                $sessionId,
                $studentId
            );

            $intervention = null;
            if ($hasIntervention) {
                $detection = $this->radmService->getLatestUnacknowledgedIntervention(
                    $sessionId,
                    $studentId
                );

                if ($detection) {
                    $intervention = [
                        'detection_id' => $detection->id,
                        'rai_score' => $detection->rai_score,
                        'created_at' => $detection->created_at->toISOString(),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'has_intervention' => $hasIntervention,
                'intervention' => $intervention
            ]);

        } catch (\Exception $e) {
            \Log::error('RADM check intervention error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check interventions'
            ], 500);
        }
    }

    /**
     * Get RADM statistics for a session
     * 
     * GET /api/radm/statistics
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sessionId' => 'required|string',
            'studentId' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sessionId = $request->input('sessionId');
            $studentId = $request->input('studentId');

            $statistics = $this->radmService->getSessionStatistics($sessionId, $studentId);

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            \Log::error('RADM statistics error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics'
            ], 500);
        }
    }
}
