<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pointsPerLevel = 60;
        $levels = [];

        for ($level = 1; $level <= 100; $level++) {
            $pointsRequired = ($level - 1) * $pointsPerLevel;

            $levels[] = [
                'level' => $level,
                'points_required' => $pointsRequired,
                'points_for_this_level' => $level === 1 ? 0 : $pointsPerLevel,
            ];
        }

        DB::table('level_config')->insert($levels);
    }
}
