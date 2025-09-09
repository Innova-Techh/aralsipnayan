<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RankConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ranks = [
            ['rank_name' => 'Math Explorer', 'required_level' => 10, 'rank_description' => 'Starting the journey'],
            ['rank_name' => 'Math Adventurer', 'required_level' => 20, 'rank_description' => 'Gaining confidence and exploring new challenges'],
            ['rank_name' => 'Math Seeker', 'required_level' => 30, 'rank_description' => 'Developing problem-solving skills and curiosity'],
            ['rank_name' => 'Math Strategist', 'required_level' => 40, 'rank_description' => 'Learning to think critically and apply strategies'],
            ['rank_name' => 'Math Innovator', 'required_level' => 50, 'rank_description' => 'Solving problems creatively and independently'],
            ['rank_name' => 'Math Prodigy', 'required_level' => 60, 'rank_description' => 'Recognized for impressive math mastery and speed'],
            ['rank_name' => 'Math Virtuoso', 'required_level' => 70, 'rank_description' => 'Demonstrating exceptional mathematical skills'],
            ['rank_name' => 'Math Sage', 'required_level' => 80, 'rank_description' => 'Reaching a higher understanding of concepts and patterns'],
            ['rank_name' => 'Math Champion', 'required_level' => 90, 'rank_description' => 'Competing at an elite level and mastering challenges'],
            ['rank_name' => 'Math Grandmaster', 'required_level' => 100, 'rank_description' => 'The ultimate achievement'],
        ];

        DB::table('rank_config')->insert($ranks);
    }
}
