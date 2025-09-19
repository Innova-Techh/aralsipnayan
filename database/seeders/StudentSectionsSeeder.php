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
        $sections = ['A', 'B', 'C'];
        $schoolYear = '2024-2025';
        $schoolName = 'Pembo Elementary School';

        $firstNames = ['John', 'Jane', 'Carlos', 'Maria', 'Liam', 'Emma', 'Noah', 'Olivia', 'Ethan', 'Ava'];
        $middleNames = ['Michael', 'Grace', 'Santos', 'Reyes', 'Anne', 'David', 'Timothy', 'Rose', 'James', 'Mae'];
        $lastNames = ['Cruz', 'Santos', 'Reyes', 'Garcia', 'Dela Cruz', 'Bautista', 'Torres', 'Flores', 'Ramos', 'Aquino'];

        foreach ($sections as $sectionIndex => $section) {
            for ($i = 1; $i <= 5; $i++) {
                $username = 'student' . strtolower($section) . $i; // studenta1, studentb1, studentc1
                $email = $username . '@example.com';

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
                    'has_completed_onboarding' => false,
                    'onboarding_completed_at' => null,
                    'is_first_login' => true,

                    // Assessments
                    'has_viewed_assessments' => false,
                    'first_assessment_view_at' => null,

                    // Gamification
                    'current_streak' => 0,
                    'longest_streak' => 0,
                    'last_activity_date' => null,
                    'total_points' => 0,

                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}


