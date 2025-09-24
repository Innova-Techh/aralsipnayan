<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LevelUpController extends Controller
{
    /**
     * Base XP required for level 1
     */
    const BASE_XP = 250;
    
    /**
     * XP increment per level
     */
    const XP_INCREMENT = 50;
    
    /**
     * Calculate level from XP
     */
    public function calculateLevel(int $xp): int
    {
        if ($xp < self::BASE_XP) {
            return 1;
        }
        
        // Level calculation: Find the highest level where required XP <= current XP
        $level = 1;
        $totalXpRequired = 0;
        
        while (true) {
            $xpForNextLevel = self::BASE_XP + (($level - 1) * self::XP_INCREMENT);
            if ($totalXpRequired + $xpForNextLevel > $xp) {
                break;
            }
            $totalXpRequired += $xpForNextLevel;
            $level++;
            
            // Safety cap at level 100
            if ($level > 100) break;
        }
        
        return $level;
    }
    
    /**
     * Calculate XP required for a specific level
     */
    public function getXpRequiredForLevel(int $level): int
    {
        if ($level <= 1) return 0;
        
        $totalXp = 0;
        for ($i = 1; $i < $level; $i++) {
            $totalXp += self::BASE_XP + (($i - 1) * self::XP_INCREMENT);
        }
        
        return $totalXp;
    }
    
    /**
     * Get XP needed for next level
     */
    public function getXpForNextLevel(int $currentLevel): int
    {
        return self::BASE_XP + (($currentLevel - 1) * self::XP_INCREMENT);
    }
    
    /**
     * Get progress information for current XP
     */
    public function getProgressInfo(int $userXp): array
    {
        $currentLevel = $this->calculateLevel($userXp);
        $xpRequiredForCurrentLevel = $this->getXpRequiredForLevel($currentLevel);
        $xpForNextLevel = $this->getXpForNextLevel($currentLevel);
        $xpRequiredForNextLevel = $xpRequiredForCurrentLevel + $xpForNextLevel;
        
        $currentLevelXp = $userXp - $xpRequiredForCurrentLevel;
        $progressPercentage = ($currentLevelXp / $xpForNextLevel) * 100;
        $xpRemaining = $xpForNextLevel - $currentLevelXp;
        
        $isMaxLevel = $currentLevel >= 100;
        
        return [
            'current_level' => $currentLevel,
            'current_xp' => $userXp,
            'current_level_xp' => $currentLevelXp,
            'xp_for_next_level' => $xpForNextLevel,
            'xp_remaining' => $xpRemaining,
            'progress_percentage' => min(100, max(0, $progressPercentage)),
            'is_max_level' => $isMaxLevel,
            'rank_info' => $this->getRankInfo($currentLevel),
            'tier_info' => $this->getTierInfo($currentLevel)
        ];
    }
    
    /**
     * Get rank information based on level
     */
    public function getRankInfo(int $level): array
    {
        $ranks = [
            ['min' => 1, 'max' => 10, 'title' => 'Math Explorer', 'description' => 'Starting the journey', 'image' => 'rank-1.png'],
            ['min' => 11, 'max' => 20, 'title' => 'Math Adventurer', 'description' => 'Gaining confidence and exploring new challenges', 'image' => 'rank-2.png'],
            ['min' => 21, 'max' => 30, 'title' => 'Math Seeker', 'description' => 'Developing problem-solving skills and curiosity', 'image' => 'rank-3.png'],
            ['min' => 31, 'max' => 40, 'title' => 'Math Strategist', 'description' => 'Learning to think critically and apply strategies', 'image' => 'rank-4.png'],
            ['min' => 41, 'max' => 50, 'title' => 'Math Innovator', 'description' => 'Solving problems creatively and independently', 'image' => 'rank-5.png'],
            ['min' => 51, 'max' => 60, 'title' => 'Math Prodigy', 'description' => 'Recognized for impressive math mastery and speed', 'image' => 'rank-6.png'],
            ['min' => 61, 'max' => 70, 'title' => 'Math Virtuoso', 'description' => 'Demonstrating exceptional mathematical skills', 'image' => 'rank-7.png'],
            ['min' => 71, 'max' => 80, 'title' => 'Math Sage', 'description' => 'Reaching a higher understanding of concepts and patterns', 'image' => 'rank-8.png'],
            ['min' => 81, 'max' => 90, 'title' => 'Math Champion', 'description' => 'Competing at the highest level and mastering complexity', 'image' => 'rank-9.png'],
            ['min' => 91, 'max' => 100, 'title' => 'Math Grandmaster', 'description' => 'The ultimate achievement', 'image' => 'rank-10.png'],
        ];
        
        foreach ($ranks as $rank) {
            if ($level >= $rank['min'] && $level <= $rank['max']) {
                return $rank;
            }
        }
        
        // Fallback
        return $ranks[0];
    }
    
    /**
     * Get tier styling information based on level
     */
    public function getTierInfo(int $level): array
    {
        $tiers = [
            // Bronze (Levels 1-10)
            [
                'min' => 1, 'max' => 10, 'name' => 'Bronze',
                'card_gradient' => 'from-[#C77C3E] to-[#5A2E12]',
                'card_stroke' => 'border-[#3B1F0C]',
                'badge_gradient' => 'from-[#E69B56] to-[#5A2E12]',
                'badge_stroke' => 'border-[#3B1F0C]',
                'badge_shadow' => 'drop-shadow-[0_4px_0_#4D2A12]',
                'title_color' => 'text-white',
                'title_stroke' => 'text-outline-custom-[#3B1F0C]',
                'xp_color' => 'text-[#FFF3E6]',
                'xp_stroke' => 'text-outline-custom-[#4D2A12]',
                'xp_shadow' => 'drop-shadow-[0_3px_0_#4D2A12]',
                'labels_color' => 'text-[#FFD9B3]',
                'message_color' => 'text-[#E0C3A0]',
                'button_gradient' => 'from-[#E69B56] to-[#5A2E12]',
                'button_stroke' => 'border-[#4D2A12]',
                'button_shadow' => 'drop-shadow-[0_4px_0_#4D2A12]',
                'button_text_stroke' => 'text-outline-custom-[#4D2A12]',
                'button_text_shadow' => 'drop-shadow-[0_3px_0_#4D2A12]'
            ],
            // Silver (Levels 11-20)
            [
                'min' => 11, 'max' => 20, 'name' => 'Silver',
                'card_gradient' => 'from-[#D9E3F2] to-[#3C4757]',
                'card_stroke' => 'border-[#2A313D]',
                'badge_gradient' => 'from-[#F2F6FA] to-[#3C4757]',
                'badge_stroke' => 'border-[#2A313D]',
                'badge_shadow' => 'drop-shadow-[0_4px_0_#2E3642]',
                'title_color' => 'text-white',
                'title_stroke' => 'text-outline-custom-[#2A313D]',
                'xp_color' => 'text-[#F9FBFF]',
                'xp_stroke' => 'text-outline-custom-[#2E3642]',
                'xp_shadow' => 'drop-shadow-[0_3px_0_#2E3642]',
                'labels_color' => 'text-[#E2E8F3]',
                'message_color' => 'text-white',
                'button_gradient' => 'from-[#F2F6FA] to-[#3C4757]',
                'button_stroke' => 'border-[#2E3642]',
                'button_shadow' => 'drop-shadow-[0_4px_0_#2E3642]',
                'button_text_stroke' => 'text-outline-custom-[#2E3642]',
                'button_text_shadow' => 'drop-shadow-[0_3px_0_#2E3642]'
            ],
            // Gold Early (Levels 21-30)
            [
                'min' => 21, 'max' => 30, 'name' => 'Gold Early',
                'card_gradient' => 'from-[#D9E3F2] to-[#3C4757]',
                'card_stroke' => 'border-[#2A313D]',
                'badge_gradient' => 'from-[#F2F6FA] to-[#3C4757]',
                'badge_stroke' => 'border-[#2A313D]',
                'badge_shadow' => 'drop-shadow-[0_4px_0_#2E3642]',
                'title_color' => 'text-white',
                'title_stroke' => 'text-outline-custom-[#5A3A00]',
                'xp_color' => 'text-[#F9FBFF]',
                'xp_stroke' => 'text-outline-custom-[#2E3642]',
                'xp_shadow' => 'drop-shadow-[0_3px_0_#2E3642]',
                'labels_color' => 'text-[#E2E8F3]',
                'message_color' => 'text-white',
                'button_gradient' => 'from-[#F2F6FA] to-[#3C4757]',
                'button_stroke' => 'border-[#2E3642]',
                'button_shadow' => 'drop-shadow-[0_4px_0_#2E3642]',
                'button_text_stroke' => 'text-outline-custom-[#2E3642]',
                'button_text_shadow' => 'drop-shadow-[0_3px_0_#2E3642]'
            ],
            // Gold (Levels 31-40)
            [
                'min' => 31, 'max' => 40, 'name' => 'Gold',
                'card_gradient' => 'from-[#FFD55C] to-[#7A4B0E]',
                'card_stroke' => 'border-[#4D3009]',
                'badge_gradient' => 'from-[#FFE58A] to-[#7A4B0E]',
                'badge_stroke' => 'border-[#4D3009]',
                'badge_shadow' => 'drop-shadow-[0_4px_0_#5C3A0F]',
                'title_color' => 'text-white',
                'title_stroke' => 'text-outline-custom-[#4D3009]',
                'xp_color' => 'text-[#FFF7E6]',
                'xp_stroke' => 'text-outline-custom-[#5C3A0F]',
                'xp_shadow' => 'drop-shadow-[0_3px_0_#5C3A0F]',
                'labels_color' => 'text-[#FFECCC]',
                'message_color' => 'text-white',
                'button_gradient' => 'from-[#FFE58A] to-[#7A4B0E]',
                'button_stroke' => 'border-[#5C3A0F]',
                'button_shadow' => 'drop-shadow-[0_4px_0_#5C3A0F]',
                'button_text_stroke' => 'text-outline-custom-[#5C3A0F]',
                'button_text_shadow' => 'drop-shadow-[0_3px_0_#5C3A0F]'
            ],
            // Topaz (Levels 41-50)
            [
                'min' => 41, 'max' => 50, 'name' => 'Topaz',
                'card_gradient' => 'from-[#F6A43B] to-[#A64906]',
                'card_stroke' => 'border-[#7C3304]',
                'badge_gradient' => 'from-[#FFD59E] to-[#B65A0B]',
                'badge_stroke' => 'border-[#9C5B0C]',
                'badge_shadow' => 'drop-shadow-[0_4px_0_#663308]',
                'title_color' => 'text-white',
                'title_stroke' => 'text-outline-custom-[#9C5B0C]',
                'xp_color' => 'text-[#FFF2E2]',
                'xp_stroke' => 'text-outline-custom-[#9C5B0C]',
                'xp_shadow' => 'drop-shadow-[0_3px_0_#9C5B0C]',
                'labels_color' => 'text-[#FFE9D1]',
                'message_color' => 'text-white',
                'button_gradient' => 'from-[#FFB74A] to-[#A64906]',
                'button_stroke' => 'border-[#5A2503]',
                'button_shadow' => 'drop-shadow-[0_4px_0_#5A2503]',
                'button_text_stroke' => 'text-outline-custom-[#5A2503]',
                'button_text_shadow' => 'drop-shadow-[0_3px_0_#5A2503]'
            ],
            // Emerald (Levels 51-60)
            [
                'min' => 51, 'max' => 60, 'name' => 'Emerald',
                'card_gradient' => 'from-[#10B981] to-[#047857]',
                'card_stroke' => 'border-[#064E3B]',
                'badge_gradient' => 'from-[#34D399] to-[#059669]',
                'badge_stroke' => 'border-[#065F46]',
                'badge_shadow' => 'drop-shadow-[0_4px_0_#064E3B]',
                'title_color' => 'text-white',
                'title_stroke' => 'text-outline-custom-[#064E3B]',
                'xp_color' => 'text-[#ECFDF5]',
                'xp_stroke' => 'text-outline-custom-[#065F46]',
                'xp_shadow' => 'drop-shadow-[0_3px_0_#065F46]',
                'labels_color' => 'text-[#D1FAE5]',
                'message_color' => 'text-white',
                'button_gradient' => 'from-[#34D399] to-[#059669]',
                'button_stroke' => 'border-[#064E3B]',
                'button_shadow' => 'drop-shadow-[0_4px_0_#064E3B]',
                'button_text_stroke' => 'text-outline-custom-[#064E3B]',
                'button_text_shadow' => 'drop-shadow-[0_3px_0_#064E3B]'
            ],
            // Ruby (Levels 61-70)
            [
                'min' => 61, 'max' => 70, 'name' => 'Ruby',
                'card_gradient' => 'from-[#DC2626] to-[#991B1B]',
                'card_stroke' => 'border-[#7F1D1D]',
                'badge_gradient' => 'from-[#F87171] to-[#B91C1C]',
                'badge_stroke' => 'border-[#991B1B]',
                'badge_shadow' => 'drop-shadow-[0_4px_0_#7F1D1D]',
                'title_color' => 'text-white',
                'title_stroke' => 'text-outline-custom-[#7F1D1D]',
                'xp_color' => 'text-[#FEF2F2]',
                'xp_stroke' => 'text-outline-custom-[#991B1B]',
                'xp_shadow' => 'drop-shadow-[0_3px_0_#991B1B]',
                'labels_color' => 'text-[#FECACA]',
                'message_color' => 'text-white',
                'button_gradient' => 'from-[#F87171] to-[#B91C1C]',
                'button_stroke' => 'border-[#7F1D1D]',
                'button_shadow' => 'drop-shadow-[0_4px_0_#7F1D1D]',
                'button_text_stroke' => 'text-outline-custom-[#7F1D1D]',
                'button_text_shadow' => 'drop-shadow-[0_3px_0_#7F1D1D]'
            ],
            // Amethyst (Levels 71-80)
            [
                'min' => 71, 'max' => 80, 'name' => 'Amethyst',
                'card_gradient' => 'from-[#8B5CF6] to-[#6D28D9]',
                'card_stroke' => 'border-[#581C87]',
                'badge_gradient' => 'from-[#A78BFA] to-[#7C3AED]',
                'badge_stroke' => 'border-[#6D28D9]',
                'badge_shadow' => 'drop-shadow-[0_4px_0_#581C87]',
                'title_color' => 'text-white',
                'title_stroke' => 'text-outline-custom-[#581C87]',
                'xp_color' => 'text-[#FAF5FF]',
                'xp_stroke' => 'text-outline-custom-[#6D28D9]',
                'xp_shadow' => 'drop-shadow-[0_3px_0_#6D28D9]',
                'labels_color' => 'text-[#E9D5FF]',
                'message_color' => 'text-white',
                'button_gradient' => 'from-[#A78BFA] to-[#7C3AED]',
                'button_stroke' => 'border-[#581C87]',
                'button_shadow' => 'drop-shadow-[0_4px_0_#581C87]',
                'button_text_stroke' => 'text-outline-custom-[#581C87]',
                'button_text_shadow' => 'drop-shadow-[0_3px_0_#581C87]'
            ],
            // Sapphire (Levels 81-90)
            [
                'min' => 81, 'max' => 90, 'name' => 'Sapphire',
                'card_gradient' => 'from-[#3B82F6] to-[#1E40AF]',
                'card_stroke' => 'border-[#1E3A8A]',
                'badge_gradient' => 'from-[#60A5FA] to-[#2563EB]',
                'badge_stroke' => 'border-[#1D4ED8]',
                'badge_shadow' => 'drop-shadow-[0_4px_0_#1E3A8A]',
                'title_color' => 'text-white',
                'title_stroke' => 'text-outline-custom-[#1E3A8A]',
                'xp_color' => 'text-[#EFF6FF]',
                'xp_stroke' => 'text-outline-custom-[#1D4ED8]',
                'xp_shadow' => 'drop-shadow-[0_3px_0_#1D4ED8]',
                'labels_color' => 'text-[#DBEAFE]',
                'message_color' => 'text-white',
                'button_gradient' => 'from-[#60A5FA] to-[#2563EB]',
                'button_stroke' => 'border-[#1E3A8A]',
                'button_shadow' => 'drop-shadow-[0_4px_0_#1E3A8A]',
                'button_text_stroke' => 'text-outline-custom-[#1E3A8A]',
                'button_text_shadow' => 'drop-shadow-[0_3px_0_#1E3A8A]'
            ],
            // Diamond (Levels 91-100)
            [
                'min' => 91, 'max' => 100, 'name' => 'Diamond',
                'card_gradient' => 'from-[#F8FAFC] to-[#64748B]',
                'card_stroke' => 'border-[#475569]',
                'badge_gradient' => 'from-[#FFFFFF] to-[#94A3B8]',
                'badge_stroke' => 'border-[#64748B]',
                'badge_shadow' => 'drop-shadow-[0_4px_0_#475569]',
                'title_color' => 'text-gray-800',
                'title_stroke' => 'text-outline-custom-[#475569]',
                'xp_color' => 'text-[#0F172A]',
                'xp_stroke' => 'text-outline-custom-[#64748B]',
                'xp_shadow' => 'drop-shadow-[0_3px_0_#64748B]',
                'labels_color' => 'text-[#334155]',
                'message_color' => 'text-gray-800',
                'button_gradient' => 'from-[#FFFFFF] to-[#94A3B8]',
                'button_stroke' => 'border-[#475569]',
                'button_shadow' => 'drop-shadow-[0_4px_0_#475569]',
                'button_text_stroke' => 'text-outline-custom-[#475569]',
                'button_text_shadow' => 'drop-shadow-[0_3px_0_#475569]'
            ]
        ];
        
        foreach ($tiers as $tier) {
            if ($level >= $tier['min'] && $level <= $tier['max']) {
                return $tier;
            }
        }
        
        // Fallback to bronze
        return $tiers[0];
    }
    
    /**
     * Simulate adding XP and check for level up
     */
    public function addXP(Request $request): JsonResponse
    {
        $xpToAdd = $request->input('xp', 100);
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        $currentXp = $user->xp ?? 460;
        $oldLevel = $this->calculateLevel($currentXp);
        
        $newXp = $currentXp + $xpToAdd;
        $newLevel = $this->calculateLevel($newXp);
        
        // Update user XP (you should implement actual database update)
        // $user->update(['xp' => $newXp]);
        
        $leveledUp = $newLevel > $oldLevel;
        
        $response = [
            'old_xp' => $currentXp,
            'new_xp' => $newXp,
            'xp_added' => $xpToAdd,
            'old_level' => $oldLevel,
            'new_level' => $newLevel,
            'leveled_up' => $leveledUp,
            'progress_info' => $this->getProgressInfo($newXp)
        ];
        
        if ($leveledUp) {
            $response['level_up_data'] = [
                'levels_gained' => $newLevel - $oldLevel,
                'new_rank' => $this->getRankInfo($newLevel),
                'new_tier' => $this->getTierInfo($newLevel),
                'rewards' => $this->calculateLevelUpRewards($oldLevel, $newLevel)
            ];
        }
        
        return response()->json($response);
    }
    
    /**
     * Calculate rewards for leveling up
     */
    private function calculateLevelUpRewards(int $oldLevel, int $newLevel): array
    {
        $rewards = [];
        $levelsGained = $newLevel - $oldLevel;
        
        // Base rewards per level
        $basePointsPerLevel = 50;
        $bonusMultiplier = 1 + ($newLevel * 0.1); // Bonus increases with level
        
        $totalPoints = intval($basePointsPerLevel * $levelsGained * $bonusMultiplier);
        
        $rewards[] = [
            'type' => 'points',
            'amount' => $totalPoints,
            'message' => "Earned {$totalPoints} bonus points!"
        ];
        
        // Special rewards for tier changes
        $oldTier = $this->getTierInfo($oldLevel);
        $newTier = $this->getTierInfo($newLevel);
        
        if ($oldTier['name'] !== $newTier['name']) {
            $rewards[] = [
                'type' => 'tier_unlock',
                'tier' => $newTier['name'],
                'message' => "Unlocked {$newTier['name']} tier!"
            ];
        }
        
        return $rewards;
    }
    
    /**
     * Get current progress for dashboard
     */
    public function getCurrentProgress(): JsonResponse
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        $currentXp = $user->xp ?? 460;
        $progressInfo = $this->getProgressInfo($currentXp);
        
        return response()->json($progressInfo);
    }
}