<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index()
    {
        // All achievements with rarity levels
        $allAchievements = [
            [
                'id' => 1,
                'title' => 'First Steps',
                'description' => 'Complete your first lesson',
                'icon' => 'book',
                'rarity' => 'Common',
                'rarity_color' => 'gray',
                'is_earned' => true,
                'earned_date' => '2024-01-15'
            ],
            [
                'id' => 2,
                'title' => 'Quick Learner',
                'description' => 'Complete 5 lessons in a day',
                'icon' => 'lightning',
                'rarity' => 'Uncommon',
                'rarity_color' => 'green',
                'is_earned' => true,
                'earned_date' => '2024-01-20'
            ],
            [
                'id' => 3,
                'title' => 'Math Explorer',
                'description' => 'Try lessons from 3 different categories',
                'icon' => 'rocket',
                'rarity' => 'Rare',
                'rarity_color' => 'blue',
                'is_earned' => true,
                'earned_date' => '2024-01-25'
            ],
            [
                'id' => 4,
                'title' => 'Perfect Score',
                'description' => 'Get 100% on any quiz',
                'icon' => 'star',
                'rarity' => 'Uncommon',
                'rarity_color' => 'green',
                'is_earned' => true,
                'earned_date' => '2024-01-30'
            ],
            [
                'id' => 5,
                'title' => 'Math Whiz',
                'description' => 'Complete 20 lessons with 90%+ accuracy',
                'icon' => 'brain',
                'rarity' => 'Epic',
                'rarity_color' => 'purple',
                'is_earned' => false,
                'earned_date' => null
            ],
            [
                'id' => 6,
                'title' => 'Addition Master',
                'description' => 'Master all addition concepts',
                'icon' => 'medal',
                'rarity' => 'Rare',
                'rarity_color' => 'blue',
                'is_earned' => false,
                'earned_date' => null
            ],
            [
                'id' => 7,
                'title' => 'Subtraction Master',
                'description' => 'Master all subtraction concepts',
                'icon' => 'medal',
                'rarity' => 'Rare',
                'rarity_color' => 'blue',
                'is_earned' => false,
                'earned_date' => null
            ],
            [
                'id' => 8,
                'title' => 'Counting Master',
                'description' => 'Master all counting concepts',
                'icon' => 'medal',
                'rarity' => 'Rare',
                'rarity_color' => 'blue',
                'is_earned' => false,
                'earned_date' => null
            ],
            [
                'id' => 9,
                'title' => 'Multiplication Master',
                'description' => 'Master all multiplication concepts',
                'icon' => 'medal',
                'rarity' => 'Rare',
                'rarity_color' => 'blue',
                'is_earned' => false,
                'earned_date' => null
            ],
            [
                'id' => 10,
                'title' => 'Grade Champion',
                'description' => 'Complete all lessons in a grade level',
                'icon' => 'crown',
                'rarity' => 'Legendary',
                'rarity_color' => 'yellow',
                'is_earned' => false,
                'earned_date' => null
            ],
            [
                'id' => 11,
                'title' => 'On Fire',
                'description' => 'Maintain a 7-day learning streak',
                'icon' => 'flame',
                'rarity' => 'Epic',
                'rarity_color' => 'purple',
                'is_earned' => false,
                'earned_date' => null
            ],
            [
                'id' => 12,
                'title' => 'Point Collector',
                'description' => 'Earn 10,000 total points',
                'icon' => 'trophy',
                'rarity' => 'Uncommon',
                'rarity_color' => 'green',
                'is_earned' => false,
                'earned_date' => null
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

        return view('user.achievements', compact('filteredAchievements', 'earnedCount', 'totalCount', 'filter', 'sort'));
    }
}