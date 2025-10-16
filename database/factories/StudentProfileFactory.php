<?php

namespace Database\Factories;

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentProfile>
 */
class StudentProfileFactory extends Factory
{
    protected $model = StudentProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Filipino names
        $filipinoFirstNames = [
            'Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Antonio', 'Carmen',
            'Francisco', 'Teresa', 'Miguel', 'Isabel', 'Rafael', 'Sofia', 'Carlos',
            'Elena', 'Luis', 'Gabriela', 'Marco', 'Valentina', 'Diego', 'Camila'
        ];

        $filipinoLastNames = [
            'Reyes', 'Santos', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Lopez',
            'Gonzales', 'Rodriguez', 'Fernandez', 'Ramos', 'Rivera', 'Torres'
        ];

        $firstname = $this->faker->randomElement($filipinoFirstNames);
        $lastname = $this->faker->randomElement($filipinoLastNames);
        $middlename = $this->faker->randomElement($filipinoLastNames);

        return [
            'user_id' => User::factory(),
            'student_id' => 'STU-' . $this->faker->unique()->numberBetween(2024001, 2029999),
            'firstname' => $firstname,
            'lastname' => $lastname,
            'middlename' => $middlename,
            'section' => $this->faker->randomElement([
                'Grade 7 - Section A',
                'Grade 7 - Section B',
                'Grade 8 - Section A',
                'Grade 8 - Section B',
                'Grade 9 - Section A',
                'Grade 9 - Section B',
            ]),
            'grade_level' => $this->faker->randomElement(['7', '8', '9', '10']),
            'school_name' => 'Pembo Elementary School',
            'school_year' => '2024-2025',
            'avatar_url' => '/images/profile/default.png',
            'has_completed_onboarding' => $this->faker->boolean(80),
            'onboarding_completed_at' => $this->faker->optional(0.8)->dateTimeBetween('-60 days', 'now'),
            'is_first_login' => $this->faker->boolean(20),
            'has_viewed_assessments' => $this->faker->boolean(70),
            'first_assessment_view_at' => $this->faker->optional(0.7)->dateTimeBetween('-45 days', 'now'),
            'current_streak' => $this->faker->numberBetween(0, 15),
            'longest_streak' => $this->faker->numberBetween(5, 30),
            'last_activity_date' => $this->faker->optional(0.8)->dateTimeBetween('-7 days', 'now'),
            'total_points' => $this->faker->numberBetween(0, 5000),
            'created_at' => now()->subDays($this->faker->numberBetween(1, 90)),
            'updated_at' => now()->subDays($this->faker->numberBetween(0, 30)),
        ];
    }
}