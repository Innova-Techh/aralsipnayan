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
            $table->enum('competency', ['number_algebra', 'measurement_geometry', 'data_probability']);
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced']);
            $table->string('topic_tag', 100);

            // Question content
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'fill_blanks', 'true_false', 'drag_drop', 'connect_dots'])
                  ->default('multiple_choice');
            $table->text('choice_a')->nullable();
            $table->text('choice_b')->nullable();
            $table->text('choice_c')->nullable();
            $table->text('choice_d')->nullable();
            $table->text('correct_answer');
            $table->text('hint_text')->nullable();
            $table->text('explanation')->nullable();
            $table->integer('max_allowed_time'); // in seconds

            // Difficulty and scoring
            $table->decimal('estimated_difficulty_weight', 3, 2)->default(1.0);
            $table->enum('question_source', ['built_in', 'custom'])->default('built_in');
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->decimal('success_rate', 5, 4)->default(0.0000);
            $table->integer('base_points');
            $table->enum('blooms_taxonomy', ['remember', 'understand', 'apply', 'analyze', 'evaluate', 'create'])->default('remember');
            // Metadata
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['competency', 'difficulty_level'], 'idx_competency_difficulty');
            $table->index(['topic_tag', 'is_active'], 'idx_topic_active');
            $table->index(['difficulty_level', 'usage_count'], 'idx_difficulty_usage');
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
