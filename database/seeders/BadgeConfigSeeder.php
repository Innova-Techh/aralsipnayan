<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'badge_id' => 'first_steps',
                'badge_name' => 'First Steps',
                'badge_description' => 'Complete your first assessment (any competency)',
                'badge_type' => 'common',
                'points_required' => 0,
                'assessments_required' => 1,
                'special_condition' => 'first_assessment',
                'badge_icon_url' => 'images/achievements/a/firststep.png',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'badge_id' => 'quick_learner',
                'badge_name' => 'Quick Learner',
                'badge_description' => 'Earn 50 total points',
                'badge_type' => 'uncommon',
                'points_required' => 50,
                'assessments_required' => null,
                'special_condition' => null,
                'badge_icon_url' => 'images/achievements/a/quicklearner.png',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'badge_id' => 'on_fire',
                'badge_name' => 'On Fire',
                'badge_description' => 'Earn 150 total points',
                'badge_type' => 'rare',
                'points_required' => 150,
                'assessments_required' => null,
                'special_condition' => null,
                'badge_icon_url' => 'images/achievements/a/onfire.png',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'badge_id' => 'math_explorer_badge',
                'badge_name' => 'Math Explorer',
                'badge_description' => 'Earn 300 total points',
                'badge_type' => 'common',
                'points_required' => 300,
                'assessments_required' => null,
                'special_condition' => null,
                'badge_icon_url' => null,
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'badge_id' => 'math_whiz',
                'badge_name' => 'Math Whiz',
                'badge_description' => 'Earn 500 total points',
                'badge_type' => 'epic',
                'points_required' => 500,
                'assessments_required' => null,
                'special_condition' => null,
                'badge_icon_url' => 'images/achievements/a/mathwhiz.png',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'badge_id' => 'grade_champion',
                'badge_name' => 'Grade Champion',
                'badge_description' => 'Earn 750 total points',
                'badge_type' => 'legendary',
                'points_required' => 750,
                'assessments_required' => null,
                'special_condition' => null,
                'badge_icon_url' => 'images/achievements/a/gradechampion.png',
                'is_active' => true,
                'created_at' => now(),
            ],
        ];

        DB::table('badge_config')->insert($badges);
    }
}
