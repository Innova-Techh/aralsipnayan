<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrophiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trophies = [
            [
                'trophy_id' => 'class_weekly_leader',
                'trophy_name' => 'Class Leader Board Trophy',
                'trophy_type' => 'class_weekly',
                'trophy_description' => 'Top 10 students in class by total points earned each week',
                'max_winners' => 10,
                'reset_frequency' => 'weekly',
                'trophy_icon_url' => null,
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'trophy_id' => 'school_monthly_leader',
                'trophy_name' => 'School Leaderboard Trophy',
                'trophy_type' => 'school_monthly',
                'trophy_description' => 'Top 20 students in school by total points earned each month',
                'max_winners' => 20,
                'reset_frequency' => 'monthly',
                'trophy_icon_url' => null,
                'is_active' => true,
                'created_at' => now(),
            ],
        ];

        DB::table('trophies')->insert($trophies);
    }
}
