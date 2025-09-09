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
        Schema::create('questions', function (Blueprint $table) {
            $table->string('question_id', 50)->primary();

            // Competency & Difficulty Level
            $table->enum('competency', ['number_algebra', 'measurement_geometry', 'data_probability']);
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced']);

            // Question Info
            $table->string('topic_tag', 100); // Subcategory like 'polygons', 'fractions', etc.
            $table->enum('question_type', ['multiple_choice', 'fill_blanks', 'true_false', 'drag_drop', 'connect_dots']);
            $table->text('question_text');

            // Multiple Choice Options (nullable for non-MC questions)
            $table->text('choice_a')->nullable();
            $table->text('choice_b')->nullable();
            $table->text('choice_c')->nullable();
            $table->text('choice_d')->nullable();

            // Correct Answer (format depends on question type)
            $table->text('correct_answer');

            // Additional Question Details
            $table->text('hint_text'); // Single hint as string
            $table->text('explanation')->nullable(); // Detailed explanation for learning
            $table->integer('max_allowed_time'); // In seconds

            // Question Source & Status
            $table->enum('question_source', ['built_in', 'custom'])->default('built_in');
            $table->boolean('is_active')->default(true);

            // Gamification Points
            $table->integer('base_points'); // 3 for beginner, 6 for intermediate, 10 for advanced

            // Metadata
            $table->unsignedBigInteger('created_by')->nullable(); // Teacher_id for custom questions

            // Timestamps
            $table->timestamps();

            // Foreign Key
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for performance
            $table->index(['competency', 'difficulty_level'], 'idx_competency_difficulty');
            $table->index('topic_tag', 'idx_topic_tag');
            $table->index('is_active', 'idx_active_questions');
            $table->index('question_source', 'idx_question_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
