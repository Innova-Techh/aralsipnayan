<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        $student = User::create([
            'username' => 'student1',
            'email' => 'student1@example.com',
            'password' => Hash::make('123'), // plain password hashed
            'role' => 'Student',
            'status' => 'active',
        ]);

        $teacher = User::create([
            'username' => 'teacher1',
            'email' => 'teacher1@example.com',
            'password' => Hash::make('123'),
            'role' => 'Teacher',
            'status' => 'active',
        ]);

        $admin = User::create([
            'username' => 'admin1',
            'email' => 'admin1@example.com',
            'password' => Hash::make('123'),
            'role' => 'Admin',
            'status' => 'active',
        ]);

        // Student profile
        DB::table('student_profile')->insert([
                        'user_id' => $student->id,
                        'student_id' => 'LRN' . rand(100000, 999999),
                        'firstname' => 'John',
                        'lastname' => 'Doe',
                        'middlename' => 'Michael',
                        'gender' => 'male',
                        'section' => 'Einstein',
                        'grade_level' => '6',
                        'school_name' => 'Pembo Elementary School',
                        'school_year' => '2024-2025',

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

        // Teacher profile
        DB::table('teacher_profile')->insert([
            'user_id' => $teacher->id,
            'firstname' => 'Jane',
            'lastname' => 'Smith',
            'school_name' => 'Pembo Elementary School',
            'profile_url' => '/profiles/teacher1.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Teacher sections - Jane Smith handles sections A, B, and C
        DB::table('teacher_sections')->insert([
            [
                'teacher_id' => DB::table('teacher_profile')->where('user_id', $teacher->id)->value('id'),
                'section' => 'Einstein',
                'grade_level' => '6',
                'school_year' => '2024-2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => DB::table('teacher_profile')->where('user_id', $teacher->id)->value('id'),
                'section' => 'Newton',
                'grade_level' => '6',
                'school_year' => '2024-2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => DB::table('teacher_profile')->where('user_id', $teacher->id)->value('id'),
                'section' => 'Curie',
                'grade_level' => '6',
                'school_year' => '2024-2025',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Admin profile
        DB::table('admin_profile')->insert([
            'user_id' => $admin->id,
            'firstname' => 'Alice',
            'lastname' => 'Johnson',
            'grade_level_focus' => '6',
            'school_name' => 'Pembo Elementary School',
            'profile_url' => '/profiles/admin1.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->call([
        QuestionsTableSeeder::class,
        StudentSectionsSeeder::class,
        // add any other seeders you created
        ]);

    }
}
