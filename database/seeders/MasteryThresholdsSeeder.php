<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasteryThresholdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mastery_thresholds')->insert([
            [
                'threshold_id' => 1,
                'difficulty_level' => 'beginner',
                'min_score' => 0.00,
                'max_score' => 49.99,
                'promotion_threshold' => 50.00,
                'demotion_threshold' => 0.00,
                'questions_per_assessment' => 15,
                'proficiency_description' => 'Not Proficient + Low Proficient',
                'is_active' => true,
            ],
            [
                'threshold_id' => 2,
                'difficulty_level' => 'intermediate',
                'min_score' => 50.00,
                'max_score' => 74.99,
                'promotion_threshold' => 75.00,
                'demotion_threshold' => 49.99,
                'questions_per_assessment' => 20,
                'proficiency_description' => 'Nearly Proficient',
                'is_active' => true,
            ],
            [
                'threshold_id' => 3,
                'difficulty_level' => 'advanced',
                'min_score' => 75.00,
                'max_score' => 100.00,
                'promotion_threshold' => 100.00,
                'demotion_threshold' => 74.99,
                'questions_per_assessment' => 30,
                'proficiency_description' => 'Proficient + Highly Proficient',
                'is_active' => true,
            ],
        ]);
    }
}
