<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RankController extends Controller
{
    /**
     * Get rank information based on level
     *
     * @param int $level
     * @return array
     */
    public function getRankInfo($level)
    {
        $ranks = [
            1 => [
                'level' => 1,
                'title' => 'Math Explorer',
                'description' => 'Starting the journey',
                'image' => 'rank-1.png',
                'xp_required' => 500
            ],
            2 => [
                'level' => 2,
                'title' => 'Math Adventurer', 
                'description' => 'Gaining confidence and exploring new challenges',
                'image' => 'rank-2.png',
                'xp_required' => 1000
            ],
            3 => [
                'level' => 3,
                'title' => 'Math Seeker',
                'description' => 'Developing problem-solving skills and curiosity',
                'image' => 'rank-3.png',
                'xp_required' => 2000
            ],
            4 => [
                'level' => 4,
                'title' => 'Math Strategist',
                'description' => 'Learning to think critically and apply strategies',
                'image' => 'rank-4.png',
                'xp_required' => 3500
            ],
            5 => [
                'level' => 5,
                'title' => 'Math Innovator',
                'description' => 'Solving problems creatively and independently',
                'image' => 'rank-5.png',
                'xp_required' => 5500
            ],
            6 => [
                'level' => 6,
                'title' => 'Math Prodigy',
                'description' => 'Recognized for impressive math mastery and speed',
                'image' => 'rank-6.png',
                'xp_required' => 8000
            ],
            7 => [
                'level' => 7,
                'title' => 'Math Virtuoso',
                'description' => 'Demonstrating exceptional mathematical skills',
                'image' => 'rank-7.png',
                'xp_required' => 11500
            ],
            8 => [
                'level' => 8,
                'title' => 'Math Sage',
                'description' => 'Reaching a higher understanding of concepts and patterns',
                'image' => 'rank-8.png',
                'xp_required' => 16000
            ],
            9 => [
                'level' => 9,
                'title' => 'Math Champion',
                'description' => 'Competing at the highest level with mastery',
                'image' => 'rank-9.png',
                'xp_required' => 22000
            ],
            10 => [
                'level' => 10,
                'title' => 'Math Grandmaster',
                'description' => 'The ultimate achievement',
                'image' => 'rank-10.png',
                'xp_required' => 30000
            ]
        ];

        return $ranks[$level] ?? $ranks[1];
    }

    /**
     * Calculate user's current rank based on XP
     *
     * @param int $currentXP
     * @return int
     */
    public function calculateRank($currentXP)
    {
        $ranks = [
            1 => 500,
            2 => 1000,
            3 => 2000,
            4 => 3500,
            5 => 5500,
            6 => 8000,
            7 => 11500,
            8 => 16000,
            9 => 22000,
            10 => 30000
        ];

        $currentLevel = 1;
        foreach ($ranks as $level => $xpRequired) {
            if ($currentXP < $xpRequired) {
                break;
            }
            $currentLevel = $level;
        }

        return $currentLevel;
    }

    /**
     * Get progress information for current rank
     *
     * @param int $currentXP
     * @return array
     */
    public function getProgressInfo($currentXP)
    {
        $currentLevel = $this->calculateRank($currentXP);
        $rankInfo = $this->getRankInfo($currentLevel);
        
        // Calculate XP for current level
        $previousLevelXP = 0;
        if ($currentLevel > 1) {
            $previousRank = $this->getRankInfo($currentLevel - 1);
            $previousLevelXP = $previousRank['xp_required'];
        }
        
        $currentLevelXP = $currentXP - $previousLevelXP;
        $xpNeededForLevel = $rankInfo['xp_required'] - $previousLevelXP;
        $xpRemaining = $rankInfo['xp_required'] - $currentXP;
        
        // Calculate progress percentage
        $progressPercentage = $xpNeededForLevel > 0 ? ($currentLevelXP / $xpNeededForLevel) * 100 : 100;
        
        return [
            'current_level' => $currentLevel,
            'rank_info' => $rankInfo,
            'current_xp' => $currentXP,
            'current_level_xp' => $currentLevelXP,
            'xp_needed_for_level' => $xpNeededForLevel,
            'xp_remaining' => max(0, $xpRemaining),
            'progress_percentage' => min(100, $progressPercentage),
            'is_max_level' => $currentLevel >= 10
        ];
    }

    /**
     * Get all ranks (for admin or reference)
     *
     * @return array
     */
    public function getAllRanks()
    {
        $allRanks = [];
        for ($i = 1; $i <= 10; $i++) {
            $allRanks[] = $this->getRankInfo($i);
        }
        return $allRanks;
    }

    /**
     * Display ranks page (if you want a dedicated ranks view)
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $ranks = $this->getAllRanks();
        return view('ranks.index', compact('ranks'));
    }

    /**
     * Get user's rank data for dashboard
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserRankData(Request $request)
    {
        $user = auth()->guard('student')->user(); // Fixed authentication guard
        $userXP = $user->xp ?? 0;
        
        $progressInfo = $this->getProgressInfo($userXP);
        
        return response()->json([
            'success' => true,
            'progress_info' => $progressInfo
        ]);
    }

    /**
     * Award XP to user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function awardXP(Request $request)
    {
        $request->validate([
            'xp_amount' => 'required|integer|min:1'
        ]);

        $user = auth()->guard('student')->user(); // Fixed authentication guard
        $oldLevel = $this->calculateRank($user->xp ?? 0);
        
        // Award XP
        $user->xp = ($user->xp ?? 0) + $request->xp_amount;
        $user->save();
        
        $newLevel = $this->calculateRank($user->xp);
        $levelUp = $newLevel > $oldLevel;
        
        $progressInfo = $this->getProgressInfo($user->xp);
        
        return response()->json([
            'success' => true,
            'level_up' => $levelUp,
            'old_level' => $oldLevel,
            'new_level' => $newLevel,
            'progress_info' => $progressInfo
        ]);
    }

    /**
     * Set user's XP to specific amount (for testing purposes)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setXP(Request $request)
    {
        // Only allow in debug mode for security
        if (!config('app.debug')) {
            return response()->json(['success' => false, 'message' => 'Not available in production'], 403);
        }

        $request->validate([
            'xp_amount' => 'required|integer|min:0|max:50000'
        ]);

        $user = auth()->guard('student')->user(); // Fixed authentication guard
        $oldLevel = $this->calculateRank($user->xp ?? 0);
        
        // Set XP to exact amount
        $user->xp = $request->xp_amount;
        $user->save();
        
        $newLevel = $this->calculateRank($user->xp);
        $levelUp = $newLevel > $oldLevel;
        
        $progressInfo = $this->getProgressInfo($user->xp);
        
        return response()->json([
            'success' => true,
            'level_up' => $levelUp,
            'old_level' => $oldLevel,
            'new_level' => $newLevel,
            'progress_info' => $progressInfo,
            'message' => "XP set to {$request->xp_amount}"
        ]);
    }
}