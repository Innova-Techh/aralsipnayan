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
        Schema::create('student_mastery', function (Blueprint $table) {
            // Primary Key
            $table->string('mastery_id', 50)->primary();

            // Foreign Key: Link to Users Table
            $table->unsignedBigInteger('user_id');

            // Competency & Difficulty
            $table->enum('competency', ['number_algebra', 'measurement_geometry', 'data_probability']);
            $table->enum('current_difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');

            // Mastery Score Components (0.0 to 1.0 scale)
            $table->decimal('accuracy_score', 5, 4)->default(0.0000); // ACC = 55%
            $table->decimal('bkt_score', 5, 4)->default(0.5000);      // BKT = 45%

            // Final Mastery Score (0-100 scale)
            $table->decimal('final_mastery_score', 5, 2)->default(0.00);

            // BKT Parameters
            $table->decimal('prior_knowledge', 5, 4)->default(0.1000); // P(L0)
            $table->decimal('learn_rate', 5, 4)->default(0.3000);      // P(T)
            $table->decimal('slip_rate', 5, 4)->default(0.1000);       // P(S)
            $table->decimal('guess_rate', 5, 4)->default(0.2500);      // P(G)

            // Diagnostic Status
            $table->boolean('has_taken_diagnostic')->default(false);
            $table->timestamp('diagnostic_completed_at')->nullable();

            // Performance Tracking
            $table->integer('total_questions_answered')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('total_assessments_taken')->default(0);

            // Time Performance (for f_time calculation)
            $table->decimal('cumulative_time_score', 8, 4)->default(0.0000);

            // Metadata
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes for Optimized Queries
            $table->unique(['user_id', 'competency'], 'unique_user_competency');
            $table->index('user_id', 'idx_user_mastery');
            $table->index(['competency', 'current_difficulty'], 'idx_competency_difficulty');
            $table->index('final_mastery_score', 'idx_mastery_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_mastery');
    }
};
