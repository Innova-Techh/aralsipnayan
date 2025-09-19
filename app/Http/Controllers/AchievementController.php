<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AchievementController extends Controller
{
    /**
     * Get all achievements data (for use by other controllers)
     */
    public function getAllAchievements()
    {
        return [
            [
                'id' => 1,
                'title' => 'First Step',
                'description' => 'Completed your first assessment',
                'icon' => 'book',
                'rarity' => 'Common',
                'rarity_color' => 'gray',
                'is_earned' => true,
                'earned_date' => '2024-01-15',
                'front_image' => 'a/first_step.svg',
                'reward' => '+50 XP',
                'background_light' => '#646565'
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
                'background_light' => '#1E8646'
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
                'front_image' => 'a/math_whiz.svg',
                'reward' => '+50 XP',
                'background_light' => '#2C1B68'
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
                'front_image' => 'a/grade_champion.svg',
                'reward' => '+50 XP',
                'background_light' => '#D17A09'
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
                'front_image' => 'a/on_fire.svg',
                'reward' => '+50 XP',
                'background_light' => '#913311'
            ]
        ];
    }

    public function index()
    {
        // Get all achievements data
        $allAchievements = $this->getAllAchievements();
        
        // Achievement card color mapping and drop shadows
        $achievementColors = [
            // Blue theme
            'blue-light' => '#165A9A',
            // Brown/Orange theme
            'brown-light' => '#913311',
            // Gray theme
            'gray-light' => '#646565',
            // Green theme
            'green-light' => '#1E8646',
            // Purple theme
            'purple-light' => '#2C1B68',
            // Gold/Yellow theme
            'gold-light' => '#D17A09',
        ];
        
        // Achievement drop shadow colors
        $achievementDropShadows = [
            'achievement-common' => '4px 4px 0 #2E343C',
            'achievement-uncommon' => '4px 4px 0 #163522',
            'achievement-rare' => '4px 4px 0 #591E09',
            'achievement-epic' => '4px 4px #100A23',
            'achievement-legendary' => '4px 4px #512500',
            'achievement-blue' => '4px 4px #104373',
        ];

        // Achievement inner shadow colors
         // Achievement inner shadow colors (combined)
        $achievementInnerShadows = [
            'achievement-common' => 'inset 4px 4px 2px #525555, inset -2px 4px 4px #82868B',
            'achievement-uncommon' => 'inset 4px 4px 2px #166D38, inset -2px 4px 4px #33A15E',
            'achievement-rare' => 'inset 4px 4px 2px #591E09, inset -2px 4px 4px #591E09',
            'achievement-epic' => 'inset 4px 4px 2px #21125C, inset -2px 4px 4px #4A368B',
            'achievement-legendary' => 'inset 4px 4px 2px #804B03, inset -2px 4px 4px #FF9F4E',
            'achievement-blue' => 'inset 4px 4px 2px #104373, inset -2px 4px 4px #104373',
        ];
        
        // Function to get background color and drop shadow based on rarity
        function getBackgroundColor($rarity) {
            global $achievementColors;
            
            switch(strtolower($rarity)) {
                case 'common':
                    return $achievementColors['gray-light'];
                case 'uncommon':
                    return $achievementColors['green-light'];
                case 'rare':
                    return $achievementColors['brown-light'];
                case 'epic':
                    return $achievementColors['purple-light'];
                case 'legendary':
                    return $achievementColors['gold-light'];
                default:
                    return $achievementColors['blue-light'];
            }
        };
        
        // Function to get drop shadow based on rarity
        function getDropShadow($rarity) {
            global $achievementDropShadows;
            
            switch(strtolower($rarity)) {
                case 'common':
                    return $achievementDropShadows['achievement-common'];
                case 'uncommon':
                    return $achievementDropShadows['achievement-uncommon'];
                case 'rare':
                    return $achievementDropShadows['achievement-rare'];
                case 'epic':
                    return $achievementDropShadows['achievement-epic'];
                case 'legendary':
                    return $achievementDropShadows['achievement-legendary'];
                default:
                    return $achievementDropShadows['achievement-blue'];
            }
        };

        // Function to get inner shadow based on rarity
        function getInnerShadow($rarity) {
            global $achievementInnerShadows;
            
            switch(strtolower($rarity)) {
                case 'common':
                    return $achievementInnerShadows['achievement-common'];
                case 'uncommon':
                    return $achievementInnerShadows['achievement-uncommon'];
                case 'rare':
                    return $achievementInnerShadows['achievement-rare'];
                case 'epic':
                    return $achievementInnerShadows['achievement-epic'];
                case 'legendary':
                    return $achievementInnerShadows['achievement-legendary'];
                default:
                    return $achievementInnerShadows['achievement-blue'];
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