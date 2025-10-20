<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SectionsSeeder extends Seeder
{
    public function run(): void
    {
        $sections = ['Einstein', 'Newton', 'Curie'];
        $schoolYear = '2024-2025';
        
        foreach ($sections as $section) {
            // Check if section already exists
            $exists = DB::table('sections')
                ->where('name', $section)
                ->where('school_year', $schoolYear)
                ->exists();
            
            if (!$exists) {
                DB::table('sections')->insert([
                    'name' => $section,
                    'grade_level' => '6',
                    'school_year' => $schoolYear,
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}