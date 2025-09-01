<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index()
    {
        // All achievements with rarity levels and image paths
        $allAchievements = [
            [
                'id' => 1,
                'title' => 'First Step',
                'description' => 'Completed your first assessment',
                'icon' => 'book',
                'rarity' => 'Common',
                'rarity_color' => 'gray',
                'is_earned' => true,
                'earned_date' => '2024-01-15',
                'front_image' => 'a/firststep1.png',
                'back_image' => 'a/firststep2.png',
                'reward' => '+50 XP'
            ],
            [
                'id' => 2,
                'title' => 'Quick Learner',
                'description' => 'Completed your first assessment',
                'icon' => 'lightning',
                'rarity' => 'Uncommon',
                'rarity_color' => 'green',
                'is_earned' => true,
                'earned_date' => '2024-01-20',
                'front_image' => 'a/quicklearner1.png',
                'back_image' => 'a/quicklearner2.png',
                'reward' => '+50 XP'
            ],
            [
                'id' => 5,
                'title' => 'Math Whiz',
                'description' => 'Earn 3000+ points',
                'icon' => 'brain',
                'rarity' => 'Epic',
                'rarity_color' => 'purple',
                'is_earned' => false,
                'earned_date' => null,
                'front_image' => 'a/mathwhiz1.png',
                'back_image' => 'a/mathwhiz2.png',
                'reward' => '+50 XP'
            ],
            [
                'id' => 7,
                'title' => 'Grade Champion',
                'description' => 'Master all subtraction concepts',
                'icon' => 'medal',
                'rarity' => 'Rare',
                'rarity_color' => 'blue',
                'is_earned' => false,
                'earned_date' => null,
                'front_image' => 'a/gradechampion1.png', // Placeholder - will need actual image
                'back_image' => 'a/gradechampion2.png', // Placeholder - will need actual image
                'reward' => '+50 XP'
            ],
            [
                'id' => 11,
                'title' => 'On Fire',
                'description' => 'Earn 3000+ points',
                'icon' => 'flame',
                'rarity' => 'Epic',
                'rarity_color' => 'purple',
                'is_earned' => false,
                'earned_date' => null,
                'front_image' => 'a/onfire1.png',
                'back_image' => 'a/onfire2.png',
                'reward' => '+50 XP'
            ]
        ];

        // Filter achievements based on request
        $filter = request('filter', 'all');
        $sort = request('sort', 'default');

        $filteredAchievements = collect($allAchievements);

        // Apply filter
        if ($filter === 'earned') {
            $filteredAchievements = $filteredAchievements->where('is_earned', true);
        } elseif ($filter === 'locked') {
            $filteredAchievements = $filteredAchievements->where('is_earned', false);
        }

        // Apply sorting
        if ($sort === 'rarity') {
            $rarityOrder = ['Common' => 1, 'Uncommon' => 2, 'Rare' => 3, 'Epic' => 4, 'Legendary' => 5];
            $filteredAchievements = $filteredAchievements->sortBy(function ($achievement) use ($rarityOrder) {
                return $rarityOrder[$achievement['rarity']] ?? 0;
            });
        } elseif ($sort === 'date') {
            $filteredAchievements = $filteredAchievements->sortByDesc('earned_date');
        }

        $earnedCount = collect($allAchievements)->where('is_earned', true)->count();
        $totalCount = count($allAchievements);

        return view('student.achievements', compact('filteredAchievements', 'earnedCount', 'totalCount', 'filter', 'sort'));
    }
}