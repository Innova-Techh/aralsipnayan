<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class StudentSectionsSeeder extends Seeder
{
    public function run(): void
    {
        $schoolYear = '2024-2025';
        $schoolName = 'Pembo Elementary School';

        // Get sections from sections table
        $sections = DB::table('sections')
            ->where('school_year', $schoolYear)
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        if (empty($sections)) {
            $this->command->warn('No sections found in sections table. Please run SectionsSeeder first.');
            return;
        }

        $firstNames = ['John', 'Jane', 'Carlos', 'Maria', 'Liam', 'Emma', 'Noah', 'Olivia', 'Ethan', 'Ava'];
        $middleNames = ['Michael', 'Grace', 'Santos', 'Reyes', 'Anne', 'David', 'Timothy', 'Rose', 'James', 'Mae'];
        $lastNames = ['Cruz', 'Santos', 'Reyes', 'Garcia', 'Dela Cruz', 'Bautista', 'Torres', 'Flores', 'Ramos', 'Aquino'];

        $studentCount = 0;

        foreach ($sections as $sectionIndex => $section) {
            $this->command->info("Creating students for section: {$section}");
            
            for ($i = 1; $i <= 5; $i++) {
                $username = 'student' . strtolower($section) . $i;
                $email = $username . '@example.com';

                // Check if user already exists
                $existingUser = User::where('username', $username)->first();
                if ($existingUser) {
                    $this->command->info("User {$username} already exists. Skipping.");
                    continue;
                }

                $nameIndex = ($sectionIndex * 5) + ($i - 1);
                $firstname = $firstNames[$nameIndex % count($firstNames)];
                $middlename = $middleNames[$nameIndex % count($middleNames)];
                $lastname = $lastNames[$nameIndex % count($lastNames)];

                $user = User::create([
                    'username' => $username,
                    'email' => $email,
                    'password' => Hash::make('123'),
                    'role' => 'Student',
                    'status' => 'active',
                ]);

                DB::table('student_profile')->insert([
                    'user_id' => $user->id,
                    'student_id' => 'LRN' . ($sectionIndex + 1) . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . rand(100, 999),
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'middlename' => $middlename,
                    'section' => $section,
                    'grade_level' => '6',
                    'school_name' => $schoolName,
                    'school_year' => $schoolYear,

                    // Onboarding
                    'has_completed_onboarding' => true,
                    'onboarding_completed_at' => Carbon::now(),
                    'is_first_login' => false,

                    // Assessments
                    'has_viewed_assessments' => true,
                    'first_assessment_view_at' => Carbon::now(),

                    // Gamification
                    'current_streak' => rand(0, 10),
                    'longest_streak' => rand(0, 15),
                    'last_activity_date' => Carbon::now(),
                    'total_points' => 0,

                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $studentCount++;
            }
        }

    }
}