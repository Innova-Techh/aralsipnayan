<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class QuestionsTableSeeder extends Seeder
{
    public function run()
    {
        // Keep custom/admin-authored questions and refresh only built-in seed data.
        DB::table('questions')->where('question_source', 'built_in')->delete();

        // Path to your JSON files
        $files = [
            // DUMMY DATA
            // database_path('data/number_algebra/number_algebra_beginner.json'),
            // database_path('data/number_algebra/number_algebra_intermediate.json'),
            // database_path('data/number_algebra/number_algebra_advanced.json'),
            // database_path('data/measurement_geometry/measurement_geometry_beginner.json'),
            // database_path('data/measurement_geometry/measurement_geometry_intermediate.json'),
            // database_path('data/measurement_geometry/measurement_geometry_advanced.json'),
            // database_path('data/data_probability/data_probability_beginner.json'),
            // database_path('data/data_probability/data_probability_intermediate.json'),
            // database_path('data/data_probability/data_probability_advanced.json'),

            // EXPERT DATA
            database_path('expertdata/number_algebra/decimals_add_subtract/decimals_add_subtract_beginner.json'),
            database_path('expertdata/number_algebra/decimals_add_subtract/decimals_add_subtract_intermediate.json'),
            database_path('expertdata/number_algebra/decimals_add_subtract/decimals_add_subtract_advanced.json'),
            database_path('expertdata/number_algebra/decimals_word_problem/decimals_word_problem_beginner.json'),
            database_path('expertdata/number_algebra/decimals_word_problem/decimals_word_problem_intermediate.json'),
            database_path('expertdata/number_algebra/decimals_word_problem/decimals_word_problem_advanced.json'),
            database_path('expertdata/number_algebra/decimals_division_wordprob/decimals_division_wordprob_beginner.json'),
            database_path('expertdata/number_algebra/decimals_division_wordprob/decimals_division_wordprob_intermediate.json'),
            database_path('expertdata/number_algebra/decimals_division_wordprob/decimals_division_wordprob_advanced.json'),
            database_path('expertdata/number_algebra/decimals_division/decimals_division_beginner.json'),
            database_path('expertdata/number_algebra/decimals_division/decimals_division_intermediate.json'),
            database_path('expertdata/number_algebra/decimals_division/decimals_division_advanced.json'),
            database_path('expertdata/number_algebra/fraction_division/fraction_division_beginner.json'),
            database_path('expertdata/number_algebra/fraction_division/fraction_division_intermediate.json'),
            database_path('expertdata/number_algebra/fraction_division/fraction_division_advanced.json'),
             database_path('expertdata/number_algebra/fraction_division_wordprob/fraction_division_wordprob_beginner.json'),
            database_path('expertdata/number_algebra/fraction_division_wordprob/fraction_division_wordprob_intermediate.json'),
            database_path('expertdata/number_algebra/fraction_division_wordprob/fraction_division_wordprob_advanced.json'),
            database_path('expertdata/number_algebra/fraction_multiplication/fraction_multiplication_beginner.json'),
            database_path('expertdata/number_algebra/fraction_multiplication/fraction_multiplication_intermediate.json'),
            database_path('expertdata/number_algebra/fraction_multiplication/fraction_multiplication_advanced.json'),
            database_path('expertdata/number_algebra/gcf_lcm/gcf_lcm_beginner.json'),
            database_path('expertdata/number_algebra/gcf_lcm/gcf_lcm_intermediate.json'),
            database_path('expertdata/number_algebra/gcf_lcm/gcf_lcm_advanced.json'),
            database_path('expertdata/number_algebra/decimals_multiplication_wordprob/decimals_multiplication_wordprob_beginner.json'),
            database_path('expertdata/number_algebra/decimals_multiplication_wordprob/decimals_multiplication_wordprob_intermediate.json'),
            database_path('expertdata/number_algebra/decimals_multiplication_wordprob/decimals_multiplication_wordprob_advanced.json'),
            database_path('expertdata/number_algebra/fraction_multiplication_wordprob/fraction_multiplication_wordprob_beginner.json'),
            database_path('expertdata/number_algebra/fraction_multiplication_wordprob/fraction_multiplication_wordprob_intermediate.json'),
            database_path('expertdata/number_algebra/fraction_multiplication_wordprob/fraction_multiplication_wordprob_advanced.json'),
            database_path('expertdata/number_algebra/order_operations/order_operations_beginner.json'),
            database_path('expertdata/number_algebra/order_operations/order_operations_intermediate.json'),
            database_path('expertdata/number_algebra/order_operations/order_operations_advanced.json'),
            database_path('expertdata/number_algebra/percentage/percentage_beginner.json'),
            database_path('expertdata/number_algebra/percentage/percentage_intermediate.json'),
            database_path('expertdata/number_algebra/percentage/percentage_advanced.json'),
            

            // Measurement and Geometry
            database_path('expertdata/measurement_geometry/area_perimeter/area_perimeter_beginner.json'),
            database_path('expertdata/measurement_geometry/area_perimeter/area_perimeter_intermediate.json'),
            database_path('expertdata/measurement_geometry/area_perimeter/area_perimeter_advanced.json'),
            // Composite Area: Word Prob
            database_path('expertdata/measurement_geometry/area_composite_wordprob/area_composite_wordprob_beginner.json'),
            database_path('expertdata/measurement_geometry/area_composite_wordprob/area_composite_wordprob_intermediate.json'),
            database_path('expertdata/measurement_geometry/area_composite_wordprob/area_composite_wordprob_advanced.json'),
            // Circle Area
            database_path('expertdata/measurement_geometry/area_circle/area_circle_beginner.json'),
            database_path('expertdata/measurement_geometry/area_circle/area_circle_intermediate.json'),
            database_path('expertdata/measurement_geometry/area_circle/area_circle_advanced.json'),

            // Composite Area
            database_path('expertdata/measurement_geometry/area_composite/area_composite_beginner.json'),
            database_path('expertdata/measurement_geometry/area_composite/area_composite_intermediate.json'),
            database_path('expertdata/measurement_geometry/area_composite/area_composite_advanced.json'),

            //Circles: Word Problem
            database_path('expertdata/measurement_geometry/circles_wordprob/circles_wordprob_beginner.json'),
            database_path('expertdata/measurement_geometry/circles_wordprob/circles_wordprob_intermediate.json'),
            database_path('expertdata/measurement_geometry/circles_wordprob/circles_wordprob_advanced.json'),

            //Circumference: Circle
            database_path('expertdata/measurement_geometry/circumference_circle/circumference_circle_beginner.json'),
            database_path('expertdata/measurement_geometry/circumference_circle/circumference_circle_intermediate.json'),
            database_path('expertdata/measurement_geometry/circumference_circle/circumference_circle_advanced.json'),

            //Converting Length
            database_path('expertdata/measurement_geometry/converting_length/converting_length_beginner.json'),
            database_path('expertdata/measurement_geometry/converting_length/converting_length_intermediate.json'),
            database_path('expertdata/measurement_geometry/converting_length/converting_length_advanced.json'),
          
            //Converting Units
            database_path('expertdata/measurement_geometry/converting_units/converting_units_beginner.json'),
            database_path('expertdata/measurement_geometry/converting_units/converting_units_intermediate.json'),
            database_path('expertdata/measurement_geometry/converting_units/converting_units_advanced.json'),

             //Parts of Circle
             database_path('expertdata/measurement_geometry/parts_circle/parts_circle_beginner.json'),
            database_path('expertdata/measurement_geometry/parts_circle/parts_circle_intermediate.json'),
            database_path('expertdata/measurement_geometry/parts_circle/parts_circle_advanced.json'),

            //Tessallation
            database_path('expertdata/measurement_geometry/tessalations/tessalations_beginner.json'),
            database_path('expertdata/measurement_geometry/tessalations/tessalations_intermediate.json'),
            database_path('expertdata/measurement_geometry/tessalations/tessalations_advanced.json'),
           
            //Volume and Capacity
            database_path('expertdata/measurement_geometry/volume_capacity/volume_capacity_beginner.json'),
            database_path('expertdata/measurement_geometry/volume_capacity/volume_capacity_intermediate.json'),
            database_path('expertdata/measurement_geometry/volume_capacity/volume_capacity_advanced.json'),
        
            //Cuboid and Prism
            database_path('expertdata/measurement_geometry/cuboid_prism/cuboid_prism_beginner.json'),
            database_path('expertdata/measurement_geometry/cuboid_prism/cuboid_prism_intermediate.json'),
            database_path('expertdata/measurement_geometry/cuboid_prism/cuboid_prism_advanced.json'),

            //Volume and Capacity: Word Problem
            database_path('expertdata/measurement_geometry/volume_capacity_wordprob/volume_capacity_wordprob_beginner.json'),
            database_path('expertdata/measurement_geometry/volume_capacity_wordprob/volume_capacity_wordprob_intermediate.json'),
            database_path('expertdata/measurement_geometry/volume_capacity_wordprob/volume_capacity_wordprob_advanced.json'),
        
         //Pie Graphs
             database_path('expertdata/data_probability/pie_graphs/pie_graphs_beginner.json'),
             database_path('expertdata/data_probability/pie_graphs/pie_graphs_intermediate.json'),
              database_path('expertdata/data_probability/pie_graphs/pie_graphs_advanced.json'),
            ];


        foreach ($files as $file) {
            $json = File::get($file);
            $questions = json_decode($json, true);

            foreach ($questions as $q) {
                // Transform fields to match DB schema
                $competency = strtolower(str_replace(' ', '_', $q['competency']));
                $difficulty = strtolower($q['difficulty_level']);
                $questionType = strtolower(str_replace(' ', '_', $q['question_type']));
                $bloomsTaxonomy = strtolower($q['blooms_taxonomy'] ?? 'remember');

                // Set max_allowed_time based on difficulty
                $maxTime = match($difficulty) {
                    'beginner' => 30,
                    'intermediate' => 45,
                    'advanced' => 60,
                    default => 45,
                };

                // Set base_points based on difficulty
                $basePoints = match($difficulty) {
                    'beginner' => 3,
                    'intermediate' => 6,
                    'advanced' => 10,
                    default => 6,
                };

                DB::table('questions')->insert([
                    'question_id' => $q['question_id'],
                    'competency' => $competency,
                    'difficulty_level' => $difficulty,
                    'topic_tag' => $q['topic_tag'],
                    'question_type' => $questionType,
                    'question_text' => $q['question_text'],
                    'choice_a' => $q['choice_a'] ?? null,
                    'choice_b' => $q['choice_b'] ?? null,
                    'choice_c' => $q['choice_c'] ?? null,
                    'choice_d' => $q['choice_d'] ?? null,
                    'correct_answer' => $q['correct_answer'],
                    'hint_text' => $q['hint_text'],
                    'explanation' => $q['explanation'] ?? null,
                    'max_allowed_time' => $maxTime,
                    'question_source' => 'built_in',
                    'is_active' => true,
                    'base_points' => $basePoints,
                    'blooms_taxonomy' => $bloomsTaxonomy,
                    'created_by' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
