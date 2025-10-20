<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TeacherProfile;
use Carbon\Carbon;

class TeacherSeeder extends Seeder
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

        $teacherData = [
            [
                'firstname' => 'Jane',
                'lastname' => 'Smith',
                'username' => 'teacher1',
                'email' => 'jane.smith@example.com',
                'section' => 'Curie', // Will get first section
                'profile_url' => '/profiles/teacher1.png'
            ],
            [
                'firstname' => 'Michael',
                'lastname' => 'Johnson',
                'username' => 'teacher2',
                'email' => 'michael.johnson@example.com',
                'section' => 'Newton', // Will get second section
                'profile_url' => '/profiles/teacher2.png'
            ],
            [
                'firstname' => 'Sarah',
                'lastname' => 'Williams',
                'username' => 'teacher3',
                'email' => 'sarah.williams@example.com',
                'section' => 'Einstein', // Will get third section
                'profile_url' => '/profiles/teacher3.png'
            ]
        ];

        foreach ($teacherData as $teacherInfo) {
            // Check if user already exists
            $existingUser = User::where('username', $teacherInfo['username'])->first();
            if ($existingUser) {
                $this->command->info("User {$teacherInfo['username']} already exists. Skipping.");
                continue;
            }

            // Get section name from index
            $sectionIndex = $teacherInfo['section_index'];
            if (!isset($sections[$sectionIndex])) {
                $this->command->warn("Section index {$sectionIndex} not found. Skipping teacher {$teacherInfo['username']}.");
                continue;
            }
            $section = $sections[$sectionIndex];

            // Create user account
            $user = User::create([
                'username' => $teacherInfo['username'],
                'email' => $teacherInfo['email'],
                'password' => Hash::make('123'),
                'role' => 'Teacher',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Create teacher profile
            $teacherProfile = TeacherProfile::create([
                'user_id' => $user->id,
                'firstname' => $teacherInfo['firstname'],
                'lastname' => $teacherInfo['lastname'],
                'school_name' => $schoolName,
                'profile_url' => $teacherInfo['profile_url'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Assign teacher to their section
            DB::table('teacher_sections')->insert([
                'teacher_id' => $teacherProfile->id,
                'section' => $section,
                'grade_level' => '6',
                'school_year' => $schoolYear,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $this->command->info("Created teacher: {$teacherInfo['firstname']} {$teacherInfo['lastname']} - Section: {$section}");
        }
    }
}