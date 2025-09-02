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
                'front_image' => 'a/firststep.png',
                'reward' => '+50 XP',
                'background_light' => '#646565',
                'background_dark' => '#2E343C'
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
                'front_image' => 'a/quicklearner.png',
                'reward' => '+50 XP',
                'background_light' => '#1E8646',
                'background_dark' => '#163522'
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
                'front_image' => 'a/mathwhiz.png',
                'reward' => '+50 XP',
                'background_light' => '#2C1B68',
                'background_dark' => '#100A23'
            ],
            [
                'id' => 7,
                'title' => 'Grade Champion',
                'description' => 'Master all subtraction concepts',
                'icon' => 'medal',
                'rarity' => 'Legendary',
                'rarity_color' => 'yellow',
                'is_earned' => false,
                'earned_date' => null,
                'front_image' => 'a/gradechampion.png',
                'reward' => '+50 XP',
                'background_light' => '#D17A09',
                'background_dark' => '#512500'
            ],
            [
                'id' => 11,
                'title' => 'On Fire',
                'description' => 'Earn 3000+ points',
                'icon' => 'flame',
                'rarity' => 'Rare',
                'rarity_color' => 'red',
                'is_earned' => true,
                'earned_date' => '2024-01-20',
                'front_image' => 'a/onfire.png',
                'reward' => '+50 XP',
                'background_light' => '#913311',
                'background_dark' => '#591E09'
            ]
        ];
        
        // Achievement card color mapping
        $achievementColors = [
            // Blue theme (like your current cards)
            'blue-light' => '#165A9A',
            'blue-dark' => '#104373',
            // Brown/Orange theme
            'brown-light' => '#913311',
            'brown-dark' => '#591E09',
            // Gray theme
            'gray-light' => '#646565',
            'gray-dark' => '#2E343C',
            // Green theme
            'green-light' => '#1E8646',
            'green-dark' => '#163522',
            // Purple theme
            'purple-light' => '#2C1B68',
            'purple-dark' => '#100A23',
            // Gold/Yellow theme
            'gold-light' => '#D17A09',
            'gold-dark' => '#512500',
        ];
        
        // Function to get background colors based on rarity
        function getBackgroundColors($rarity) {
            global $achievementColors;
            
            switch(strtolower($rarity)) {
                case 'common':
                    return [
                        'light' => $achievementColors['gray-light'],
                        'dark' => $achievementColors['gray-dark']
                    ];
                case 'uncommon':
                    return [
                        'light' => $achievementColors['green-light'],
                        'dark' => $achievementColors['green-dark']
                    ];
                case 'rare':
                    return [
                        'light' => $achievementColors['brown-light'],
                        'dark' => $achievementColors['brown-dark']
                    ];
                case 'epic':
                    return [
                        'light' => $achievementColors['purple-light'],
                        'dark' => $achievementColors['purple-dark']
                    ];
                case 'legendary':
                    return [
                        'light' => $achievementColors['gold-light'],
                        'dark' => $achievementColors['gold-dark']
                    ];
                default:
                    return [
                        'light' => $achievementColors['blue-light'],
                        'dark' => $achievementColors['blue-dark']
                    ];
            }
        };

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