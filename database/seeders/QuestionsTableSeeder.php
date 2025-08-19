<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class QuestionsTableSeeder extends Seeder
{
    public function run()
    {
        // Load questions.json
        $json = File::get(database_path('data\questions.json'));
        $questions = json_decode($json, true);

        // Insert into database
        DB::table('questions')->insert($questions);
    }
}
