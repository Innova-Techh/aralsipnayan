<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_PH'); // Filipino locale for more appropriate names
        
        // Define sections
        $sections = [
            ['name' => 'Grade 7 - Section A', 'grade' => '7', 'count' => 32],
            ['name' => 'Grade 7 - Section B', 'grade' => '7', 'count' => 28],
            ['name' => 'Grade 8 - Section A', 'grade' => '8', 'count' => 30],
            ['name' => 'Grade 8 - Section B', 'grade' => '8', 'count' => 29],
            ['name' => 'Grade 9 - Section A', 'grade' => '9', 'count' => 27],
            ['name' => 'Grade 9 - Section B', 'grade' => '9', 'count' => 25],
        ];

        // Filipino first names
        $filipinoFirstNames = [
            'Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Antonio', 'Carmen',
            'Francisco', 'Teresa', 'Miguel', 'Isabel', 'Rafael', 'Sofia', 'Carlos',
            'Elena', 'Luis', 'Gabriela', 'Marco', 'Valentina', 'Diego', 'Camila',
            'Andres', 'Lucia', 'Manuel', 'Isabella', 'Ricardo', 'Victoria', 'Fernando',
            'Natalia', 'Javier', 'Daniela', 'Roberto', 'Andrea', 'Eduardo'
        ];

        // Filipino last names
        $filipinoLastNames = [
            'Reyes', 'Santos', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Lopez',
            'Gonzales', 'Rodriguez', 'Fernandez', 'Ramos', 'Rivera', 'Torres',
            'Flores', 'Castillo', 'Ramirez', 'Dela Cruz', 'Villanueva', 'Aquino',
            'Soriano', 'Morales', 'Castro', 'Santiago', 'Gutierrez', 'Domingo'
        ];

        // Status options
        $statuses = ['active', 'active', 'active', 'active', 'inactive']; // 80% active

        $studentIdCounter = 2024001;

        foreach ($sections as $section) {
            echo "Seeding {$section['count']} students for {$section['name']}...\n";
            
            for ($i = 0; $i < $section['count']; $i++) {
                $firstname = $faker->randomElement($filipinoFirstNames);
                $lastname = $faker->randomElement($filipinoLastNames);
                $middlename = $faker->randomElement($filipinoLastNames);
                $fullName = $firstname . ' ' . $lastname;
                $username = strtolower($firstname . $lastname . rand(100, 999));
                $email = strtolower($firstname . '.' . $lastname . rand(100, 999) . '@student.pembo.edu.ph');
                $studentId = 'STU-' . $studentIdCounter++;
                $status = $faker->randomElement($statuses);

                // Create user
                $userId = DB::table('users')->insertGetId([
                    'username' => $username,
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'role' => 'Student',
                    'status' => $status,
                    'last_login_date' => $faker->optional(0.7)->dateTimeBetween('-30 days', 'now'),
                    'created_at' => now()->subDays(rand(1, 90)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);

                // Create student profile
                DB::table('student_profile')->insert([
                    'user_id' => $userId,
                    'student_id' => $studentId,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'middlename' => $middlename,
                    'section' => $section['name'],
                    'grade_level' => $section['grade'],
                    'school_name' => 'Pembo Elementary School',
                    'school_year' => '2024-2025',
                    'avatar_url' => '/images/profile/default.png',
                    'has_completed_onboarding' => $faker->boolean(80), // 80% completed
                    'onboarding_completed_at' => $faker->optional(0.8)->dateTimeBetween('-60 days', 'now'),
                    'is_first_login' => $faker->boolean(20), // 20% first login
                    'has_viewed_assessments' => $faker->boolean(70),
                    'first_assessment_view_at' => $faker->optional(0.7)->dateTimeBetween('-45 days', 'now'),
                    'current_streak' => rand(0, 15),
                    'longest_streak' => rand(5, 30),
                    'last_activity_date' => $faker->optional(0.8)->dateTimeBetween('-7 days', 'now'),
                    'total_points' => rand(0, 5000),
                    'created_at' => now()->subDays(rand(1, 90)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);

                echo "  ✓ Created student: {$fullName} ({$studentId})\n";
            }
            
            echo "✓ Completed {$section['name']}\n\n";
        }

        echo "========================================\n";
        echo "Student Seeding Completed!\n";
        echo "Total students created: " . ($studentIdCounter - 2024001) . "\n";
        echo "Default password for all students: password123\n";
        echo "========================================\n";
    }
}