<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BktParametersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $competencies = ['number_algebra', 'measurement_geometry', 'data_probability'];
        $difficulties = ['beginner', 'intermediate', 'advanced'];
        $weights = ['beginner' => 0.8, 'intermediate' => 1.0, 'advanced' => 1.2];

        $data = [];

        foreach ($competencies as $competency) {
            foreach ($difficulties as $difficulty) {
                $data[] = [
                    'competency' => $competency,
                    'difficulty_level' => $difficulty,
                    'difficulty_weight' => $weights[$difficulty],
                    'created_at' => now(),
                ];
            }
        }

        DB::table('bkt_parameters')->insert($data);
    }
}
