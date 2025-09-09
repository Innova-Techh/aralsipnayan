<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
            'firstname' => 'John',
            'lastname' => 'Doe',
            'section' => 'A',
            'grade_level' => '6',
            'school_name' => 'Pembo Elementary School',
            'avatar_url' => '/avatars/default.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Teacher profile
        DB::table('teacher_profile')->insert([
            'user_id' => $teacher->id,
            'firstname' => 'Jane',
            'lastname' => 'Smith',
            'grade_level_focus' => '6',
            'school_name' => 'Pembo Elementary School',
            'profile_url' => '/profiles/teacher1.png',
            'created_at' => now(),
            'updated_at' => now(),
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
        MasteryThresholdsSeeder::class,
        BktParametersSeeder::class,
        LevelConfigSeeder::class,
        RankConfigSeeder::class,
        BadgeConfigSeeder::class,
        TrophiesSeeder::class,
        QuestionsTableSeeder::class,
        // add any other seeders you created
        ]);

    }
}
