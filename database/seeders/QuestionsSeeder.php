<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionsSeeder extends Seeder
{
    public function run()
    {
        // Number and Algebra Questions
        $questions = [
            // Beginner Level
            [
                'question_id' => 'NA-B-001',
                'competency' => 'Number_Algebra',
                'difficulty_level' => 'Beginner',
                'question_type' => 'Multiple_Choice',
                'question_text' => 'What is 5 + 3?',
                'choice_a' => '6',
                'choice_b' => '7',
                'choice_c' => '8',
                'choice_d' => '9',
                'correct_answer' => 'C',
                'hint_text' => 'Count on your fingers: 5, 6, 7, 8',
                'explanation' => '5 + 3 = 8. When adding, we combine the two numbers together.',
                'topic_tag' => 'Basic Addition',
                'points' => 5
            ],
            [
                'question_id' => 'NA-B-002',
                'competency' => 'Number_Algebra',
                'difficulty_level' => 'Beginner',
                'question_type' => 'Multiple_Choice',
                'question_text' => 'What is 10 - 4?',
                'choice_a' => '5',
                'choice_b' => '6',
                'choice_c' => '7',
                'choice_d' => '8',
                'correct_answer' => 'B',
                'hint_text' => 'Start with 10 and count backwards 4 times',
                'explanation' => '10 - 4 = 6. Subtraction means taking away.',
                'topic_tag' => 'Basic Subtraction',
                'points' => 5
            ],
            [
                'question_id' => 'NA-B-003',
                'competency' => 'Number_Algebra',
                'difficulty_level' => 'Beginner',
                'question_type' => 'Multiple_Choice',
                'question_text' => 'What is 2 × 4?',
                'choice_a' => '6',
                'choice_b' => '7',
                'choice_c' => '8',
                'choice_d' => '9',
                'correct_answer' => 'C',
                'hint_text' => 'Think of it as 2 + 2 + 2 + 2',
                'explanation' => '2 × 4 = 8. Multiplication is repeated addition.',
                'topic_tag' => 'Basic Multiplication',
                'points' => 5
            ],
            
            // Intermediate Level
            [
                'question_id' => 'NA-I-001',
                'competency' => 'Number_Algebra',
                'difficulty_level' => 'Intermediate',
                'question_type' => 'Multiple_Choice',
                'question_text' => 'Solve: 3x + 5 = 14',
                'choice_a' => 'x = 2',
                'choice_b' => 'x = 3',
                'choice_c' => 'x = 4',
                'choice_d' => 'x = 5',
                'correct_answer' => 'B',
                'hint_text' => 'Subtract 5 from both sides first',
                'explanation' => '3x + 5 = 14, so 3x = 9, therefore x = 3',
                'topic_tag' => 'Linear Equations',
                'points' => 10
            ],
            [
                'question_id' => 'NA-I-002',
                'competency' => 'Number_Algebra',
                'difficulty_level' => 'Intermediate',
                'question_type' => 'Multiple_Choice',
                'question_text' => 'What is 15% of 80?',
                'choice_a' => '10',
                'choice_b' => '12',
                'choice_c' => '14',
                'choice_d' => '16',
                'correct_answer' => 'B',
                'hint_text' => '15% = 0.15, so multiply 80 by 0.15',
                'explanation' => '15% of 80 = 0.15 × 80 = 12',
                'topic_tag' => 'Percentages',
                'points' => 10
            ],

            // Measurement and Geometry Questions
            // Beginner Level
            [
                'question_id' => 'MG-B-001',
                'competency' => 'Measurement_Geometry',
                'difficulty_level' => 'Beginner',
                'question_type' => 'Multiple_Choice',
                'question_text' => 'How many sides does a triangle have?',
                'choice_a' => '2',
                'choice_b' => '3',
                'choice_c' => '4',
                'choice_d' => '5',
                'correct_answer' => 'B',
                'hint_text' => 'Count the corners of a triangle',
                'explanation' => 'A triangle has 3 sides by definition.',
                'topic_tag' => 'Basic Shapes',
                'points' => 5
            ],
            [
                'question_id' => 'MG-B-002',
                'competency' => 'Measurement_Geometry',
                'difficulty_level' => 'Beginner',
                'question_type' => 'Multiple_Choice',
                'question_text' => 'What is the perimeter of a square with side length 4 cm?',
                'choice_a' => '12 cm',
                'choice_b' => '14 cm',
                'choice_c' => '16 cm',
                'choice_d' => '18 cm',
                'correct_answer' => 'C',
                'hint_text' => 'Add all four sides: 4 + 4 + 4 + 4',
                'explanation' => 'Perimeter of a square = 4 × side length = 4 × 4 = 16 cm',
                'topic_tag' => 'Perimeter',
                'points' => 5
            ],

            // Intermediate Level
            [
                'question_id' => 'MG-I-001',
                'competency' => 'Measurement_Geometry',
                'difficulty_level' => 'Intermediate',
                'question_type' => 'Multiple_Choice',
                'question_text' => 'What is the area of a rectangle with length 8 cm and width 5 cm?',
                'choice_a' => '35 cm²',
                'choice_b' => '40 cm²',
                'choice_c' => '45 cm²',
                'choice_d' => '50 cm²',
                'correct_answer' => 'B',
                'hint_text' => 'Area = length × width',
                'explanation' => 'Area of rectangle = length × width = 8 × 5 = 40 cm²',
                'topic_tag' => 'Area',
                'points' => 10
            ],

            // Data and Probability Questions
            // Beginner Level
            [
                'question_id' => 'DP-B-001',
                'competency' => 'Data_Probability',
                'difficulty_level' => 'Beginner',
                'question_type' => 'Multiple_Choice',
                'question_text' => 'In a class of 20 students, 12 are boys. How many are girls?',
                'choice_a' => '6',
                'choice_b' => '7',
                'choice_c' => '8',
                'choice_d' => '9',
                'correct_answer' => 'C',
                'hint_text' => 'Total students minus boys equals girls',
                'explanation' => 'Girls = Total students - Boys = 20 - 12 = 8',
                'topic_tag' => 'Basic Data',
                'points' => 5
            ],
            [
                'question_id' => 'DP-B-002',
                'competency' => 'Data_Probability',
                'difficulty_level' => 'Beginner',
                'question_type' => 'Multiple_Choice',
                'question_text' => 'What is the probability of getting heads when flipping a fair coin?',
                'choice_a' => '1/4',
                'choice_b' => '1/3',
                'choice_c' => '1/2',
                'choice_d' => '2/3',
                'correct_answer' => 'C',
                'hint_text' => 'A coin has 2 equally likely outcomes',
                'explanation' => 'Probability = favorable outcomes / total outcomes = 1/2',
                'topic_tag' => 'Basic Probability',
                'points' => 5
            ],

            // Add more questions for each competency and difficulty level
            // This is a sample - you would need about 50+ questions per competency
            // for a robust question pool
        ];

        foreach ($questions as $question) {
            DB::table('questions')->insert(array_merge($question, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}
