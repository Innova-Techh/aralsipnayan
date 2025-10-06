<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AchievementController extends Controller
{
    // Badge definitions (master list of all possible badges)
    private const BADGE_DEFINITIONS = [
        'first_steps' => [
            'title' => 'First Steps',
            'description' => 'Earn 50 total points',
            'icon' => 'book',
            'rarity' => 'Common',
            'rarity_color' => 'gray',
            'front_image' => 'a/firststep.png',
            'reward' => '+50 XP',
            'background_light' => '#646565'
        ],
        'quick_learner' => [
            'title' => 'Quick Learner',
            'description' => 'Earn 500 total points',
            'icon' => 'lightning',
            'rarity' => 'Uncommon',
            'rarity_color' => 'green',
            'front_image' => 'a/quicklearner.png',
            'reward' => '+100 XP',
            'background_light' => '#1E8646'
        ],
        'on_fire' => [
            'title' => 'On Fire',
            'description' => 'Earn 700 total points',
            'icon' => 'flame',
            'rarity' => 'Rare',
            'rarity_color' => 'red',
            'front_image' => 'a/onfire.png',
            'reward' => '+150 XP',
            'background_light' => '#913311'
        ],
        'math_whiz' => [
            'title' => 'Math Whiz',
            'description' => 'Earn 1,000 total points',
            'icon' => 'brain',
            'rarity' => 'Epic',
            'rarity_color' => 'purple',
            'front_image' => 'a/mathwhiz.png',
            'reward' => '+200 XP',
            'background_light' => '#2C1B68'
        ],
        'grade_champion' => [
            'title' => 'Grade Champion',
            'description' => 'Earn 1,500 total points',
            'icon' => 'medal',
            'rarity' => 'Legendary',
            'rarity_color' => 'yellow',
            'front_image' => 'a/gradechampion.png',
            'reward' => '+250 XP',
            'background_light' => '#D17A09'
        ],
        'math_explorer' => [
            'title' => 'Math Explorer',
            'description' => 'Earn 2,000 total points',
            'icon' => 'compass',
            'rarity' => 'Epic',
            'rarity_color' => 'purple',
            'front_image' => 'a/firststep.png',
            'reward' => '+200 XP',
            'background_light' => '#165A9A'
        ],
        'sapphire' => [
            'title' => 'Sapphire',
            'description' => 'Earn 2,500 total points',
            'icon' => 'gem',
            'rarity' => 'Epic',
            'rarity_color' => 'purple',
            'front_image' => 'a/firststep.png',
            'reward' => '+200 XP',
            'background_light' => '#165A9A'
        ],
        'ruby' => [
            'title' => 'Ruby',
            'description' => 'Earn 3,000 total points',
            'icon' => 'gem',
            'rarity' => 'Legendary',
            'rarity_color' => 'yellow',
            'front_image' => 'a/firststep.png',
            'reward' => '+250 XP',
            'background_light' => '#913311'
        ],
        'crown' => [
            'title' => 'Crown',
            'description' => 'Earn 3,500 total points',
            'icon' => 'crown',
            'rarity' => 'Legendary',
            'rarity_color' => 'yellow',
            'front_image' => 'a/firststep.png',
            'reward' => '+250 XP',
            'background_light' => '#D17A09'
        ]
    ];

    /**
     * Get all achievements data (for use by other controllers)
     */
    public function getAllAchievements()
    {
        // Use student guard to get the authenticated user ID
        $userId = Auth::guard('student')->id();

        // If user is not authenticated, return empty earned badges
        if (!$userId) {
            $earnedBadges = [];
        } else {
            // Get all badges earned by the user (including multiple instances)
            $earnedBadges = DB::table('user_badges')
                ->where('user_id', $userId)
                ->get()
                ->groupBy('badge_key')
                ->map(function ($badges) {
                    // Return the most recent badge for each badge_key
                    return $badges->sortByDesc('awarded_at')->first();
                })
                ->pluck('awarded_at', 'badge_key')
                ->toArray();
        }

        $achievements = [];
        $id = 1;

        // Build achievements array from point-based definitions
        foreach (self::BADGE_DEFINITIONS as $badgeKey => $definition) {
            $isEarned = isset($earnedBadges[$badgeKey]);

            $achievements[] = [
                'id' => $id++,
                'title' => $definition['title'],
                'description' => $definition['description'],
                'icon' => $definition['icon'],
                'rarity' => $definition['rarity'],
                'rarity_color' => $definition['rarity_color'],
                'is_earned' => $isEarned,
                'earned_date' => $isEarned ? $earnedBadges[$badgeKey] : null,
                'front_image' => $definition['front_image'],
                'reward' => $definition['reward'],
                'background_light' => $definition['background_light'],
                'badge_type' => 'points'
            ];
        }

        // Add leaderboard badge definitions
        $leaderboardBadges = \App\Http\Controllers\LeaderboardBadgeController::getLeaderboardBadgeDefinitions();
        foreach ($leaderboardBadges as $badgeKey => $definition) {
            $isEarned = isset($earnedBadges[$badgeKey]);

            $achievements[] = [
                'id' => $id++,
                'title' => $definition['name'],
                'description' => $definition['description'],
                'icon' => 'trophy',
                'rarity' => $definition['rarity'],
                'rarity_color' => $this->getRarityColor($definition['rarity']),
                'is_earned' => $isEarned,
                'earned_date' => $isEarned ? $earnedBadges[$badgeKey] : null,
                'front_image' => $this->getLeaderboardBadgeImage($badgeKey),
                'reward' => 'Top ' . ($definition['rank'] ?? ($definition['rank_min'] . '-' . $definition['rank_max'])),
                'background_light' => $definition['background_light'],
                'badge_type' => 'leaderboard'
            ];
        }

        return $achievements;
    }

    private function getRarityColor($rarity)
    {
        return match($rarity) {
            'Common' => 'gray',
            'Uncommon' => 'green',
            'Rare' => 'red',
            'Epic' => 'purple',
            'Legendary' => 'yellow',
            default => 'gray'
        };
    }

    private function getLeaderboardBadgeImage($badgeKey)
    {
        // Map leaderboard badges to images
        if (str_contains($badgeKey, '_1')) {
            return 'a/gradechampion.png'; // Gold/Champion image
        } elseif (str_contains($badgeKey, '_2') || str_contains($badgeKey, '_3')) {
            return 'a/mathwhiz.png'; // Purple/Epic image
        } else {
            return 'a/onfire.png'; // Orange/Fire image for top 5-10
        }
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