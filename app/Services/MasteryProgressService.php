<?php

namespace App\Services;

use App\Models\AssessmentSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MasteryProgressService
{
    /**
     * Get weekly mastery progress for a student over the last 6 weeks
     */
    public function getWeeklyMasteryProgress(int $userId): array
    {
        $sixWeeksAgo = Carbon::now()->subWeeks(6)->startOfWeek();
        $now = Carbon::now();

        // Get completed sessions from the last 6 weeks
        $sessions = AssessmentSession::where('user_id', $userId)
            ->where('status', 'completed')
            ->where('completed_at', '>=', $sixWeeksAgo)
            ->where('completed_at', '<=', $now)
            ->orderBy('completed_at', 'asc')
            ->get();

        if ($sessions->isEmpty()) {
            return $this->getEmptyProgressData();
        }

        // Group sessions by week
        $weeklyData = $this->groupSessionsByWeek($sessions);
        
        // Calculate overall and per-topic mastery
        $masteryData = $this->calculateMasteryMetrics($weeklyData);
        
        // Calculate stats
        $stats = $this->calculateStats($masteryData);

        return [
            'weeks' => $masteryData['weeks'],
            'mastery' => $masteryData['overall'],
            'topics' => [
                [
                    'name' => 'Number and Algebra',
                    'data' => $masteryData['number_algebra']
                ],
                [
                    'name' => 'Measurement and Geometry',
                    'data' => $masteryData['measurement_geometry']
                ],
                [
                    'name' => 'Data and Probability',
                    'data' => $masteryData['data_probability']
                ]
            ],
            'stats' => $stats
        ];
    }

    /**
     * Group sessions by week number
     */
    private function groupSessionsByWeek($sessions): array
    {
        $weeklyGroups = [];
        
        foreach ($sessions as $session) {
            $weekKey = Carbon::parse($session->completed_at)->startOfWeek()->format('Y-W');
            
            if (!isset($weeklyGroups[$weekKey])) {
                $weeklyGroups[$weekKey] = [
                    'all' => [],
                    'number_algebra' => [],
                    'measurement_geometry' => [],
                    'data_probability' => []
                ];
            }
            
            $weeklyGroups[$weekKey]['all'][] = $session;
            
            // Group by competency
            if ($session->competency === 'number_algebra') {
                $weeklyGroups[$weekKey]['number_algebra'][] = $session;
            } elseif ($session->competency === 'measurement_geometry') {
                $weeklyGroups[$weekKey]['measurement_geometry'][] = $session;
            } elseif ($session->competency === 'data_probability') {
                $weeklyGroups[$weekKey]['data_probability'][] = $session;
            } elseif ($session->competency === 'mixed') {
                // For mixed assessments, contribute to all topics
                $weeklyGroups[$weekKey]['number_algebra'][] = $session;
                $weeklyGroups[$weekKey]['measurement_geometry'][] = $session;
                $weeklyGroups[$weekKey]['data_probability'][] = $session;
            }
        }
        
        return $weeklyGroups;
    }

    /**
     * Calculate mastery metrics for each week and topic
     */
    private function calculateMasteryMetrics(array $weeklyGroups): array
    {
        $weeks = [];
        $overall = [];
        $numberAlgebra = [];
        $measurementGeometry = [];
        $dataProbability = [];
        
        // Get last 6 weeks in order
        $sixWeeksAgo = Carbon::now()->subWeeks(6)->startOfWeek();
        
        for ($i = 0; $i < 6; $i++) {
            $weekStart = $sixWeeksAgo->copy()->addWeeks($i);
            $weekKey = $weekStart->format('Y-W');
            $weekLabel = $this->getWeekLabel($weekStart);
            
            $weeks[] = $weekLabel;
            
            if (isset($weeklyGroups[$weekKey])) {
                $weekData = $weeklyGroups[$weekKey];
                
                // Calculate overall mastery for the week
                $overall[] = $this->calculateAverageMastery($weekData['all']);
                
                // Calculate per-topic mastery
                $numberAlgebra[] = $this->calculateAverageMastery($weekData['number_algebra']);
                $measurementGeometry[] = $this->calculateAverageMastery($weekData['measurement_geometry']);
                $dataProbability[] = $this->calculateAverageMastery($weekData['data_probability']);
            } else {
                // No data for this week - use previous week's value or 0
                $previousValue = !empty($overall) ? end($overall) : 0;
                $overall[] = $previousValue;
                $numberAlgebra[] = !empty($numberAlgebra) ? end($numberAlgebra) : 0;
                $measurementGeometry[] = !empty($measurementGeometry) ? end($measurementGeometry) : 0;
                $dataProbability[] = !empty($dataProbability) ? end($dataProbability) : 0;
            }
        }
        
        return [
            'weeks' => $weeks,
            'overall' => $overall,
            'number_algebra' => $numberAlgebra,
            'measurement_geometry' => $measurementGeometry,
            'data_probability' => $dataProbability
        ];
    }

    /**
     * Calculate average mastery score from sessions
     */
    private function calculateAverageMastery(array $sessions): float
    {
        if (empty($sessions)) {
            return 0;
        }
        
        $total = 0;
        $count = 0;
        
        foreach ($sessions as $session) {
            // Use final_mastery_score if available, otherwise use accuracy_percentage
            $score = $session->final_mastery_score ?? $session->accuracy_percentage ?? 0;
            $total += $score;
            $count++;
        }
        
        return $count > 0 ? round($total / $count, 2) : 0;
    }

    /**
     * Calculate statistics (current week, best week, growth)
     */
    private function calculateStats(array $masteryData): array
    {
        $overall = $masteryData['overall'];
        
        $currentWeek = !empty($overall) ? end($overall) : 0;
        $bestWeek = !empty($overall) ? max($overall) : 0;
        
        // Calculate growth (comparing first week to current week)
        $firstWeek = !empty($overall) ? $overall[0] : 0;
        $growth = $currentWeek - $firstWeek;
        
        return [
            'current_week' => round($currentWeek, 0),
            'best_week' => round($bestWeek, 0),
            'growth' => round($growth, 0),
            'growth_sign' => $growth >= 0 ? '+' : ''
        ];
    }

    /**
     * Get week label (Week 1, Week 2, etc.)
     */
    private function getWeekLabel(Carbon $weekStart): string
    {
        $weeksSince = Carbon::now()->startOfWeek()->diffInWeeks($weekStart);
        
        if ($weeksSince === 0) {
            return 'This Week';
        } elseif ($weeksSince === 1) {
            return 'Last Week';
        } else {
            return $weekStart->format('M d');
        }
    }

    /**
     * Return empty data structure when no sessions exist
     */
    private function getEmptyProgressData(): array
    {
        $weeks = [];
        $sixWeeksAgo = Carbon::now()->subWeeks(6)->startOfWeek();
        
        for ($i = 0; $i < 6; $i++) {
            $weekStart = $sixWeeksAgo->copy()->addWeeks($i);
            $weeks[] = $this->getWeekLabel($weekStart);
        }
        
        return [
            'weeks' => $weeks,
            'mastery' => [0, 0, 0, 0, 0, 0],
            'topics' => [
                ['name' => 'Number and Algebra', 'data' => [0, 0, 0, 0, 0, 0]],
                ['name' => 'Measurement and Geometry', 'data' => [0, 0, 0, 0, 0, 0]],
                ['name' => 'Data and Probability', 'data' => [0, 0, 0, 0, 0, 0]]
            ],
            'stats' => [
                'current_week' => 0,
                'best_week' => 0,
                'growth' => 0,
                'growth_sign' => ''
            ]
        ];
    }
}
