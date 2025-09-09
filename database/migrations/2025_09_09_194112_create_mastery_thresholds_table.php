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
        Schema::create('mastery_thresholds', function (Blueprint $table) {
            // Primary Key
            $table->id('threshold_id');

            // Difficulty Level
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced']);

            // Threshold Ranges (based on proficiency levels)
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);

            // Progression Rules
            $table->decimal('promotion_threshold', 5, 2)->nullable();
            $table->decimal('demotion_threshold', 5, 2)->nullable();

            // Assessment Configuration
            $table->integer('questions_per_assessment'); // e.g., 15/20/30

            // Proficiency Description
            $table->string('proficiency_description', 100)->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Constraints
            $table->unique('difficulty_level', 'unique_difficulty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mastery_thresholds');
    }
};
