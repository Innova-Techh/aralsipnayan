<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the rank_advancement points type to existing enum
        DB::statement("ALTER TABLE points_transactions MODIFY COLUMN points_type ENUM(
            'base_question', 
            'time_bonus', 
            'competency_completion', 
            'perfect_assessment', 
            'daily_triple_completion', 
            'daily_streak',
            'rank_advancement'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE points_transactions MODIFY COLUMN points_type ENUM(
            'base_question', 
            'time_bonus', 
            'competency_completion', 
            'perfect_assessment', 
            'daily_triple_completion', 
            'daily_streak'
        ) NOT NULL");
    }
};