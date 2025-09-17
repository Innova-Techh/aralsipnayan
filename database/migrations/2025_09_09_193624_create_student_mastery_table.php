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

            // Foreign Key Reference to Users Table
            $table->unsignedBigInteger('user_id');

            // Competency & Difficulty Tracking
            $table->enum('competency', ['number_algebra', 'measurement_geometry', 'data_probability']);
            $table->enum('current_difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');

            // Mastery Score Components (0.0 to 1.0 scale)
            $table->decimal('accuracy_score', 5, 4)->default(0.0000); // 55% weight
            $table->decimal('accuracy_component', 5, 4)->nullable();
            $table->decimal('bkt_score', 5, 4)->default(0.5000);      // 45% weight
            $table->decimal('bkt_component', 5, 4)->nullable();

            // Final Mastery Score (0-100 scale)
            $table->decimal('final_mastery_score', 5, 2)->default(22.50);

            // BKT Parameters
            $table->decimal('prior_knowledge', 5, 4)->default(0.1000); // P(L0)
            $table->decimal('learn_rate', 5, 4)->default(0.3000);      // P(T)
            $table->decimal('slip_rate', 5, 4)->default(0.1000);       // P(S)
            $table->decimal('guess_rate', 5, 4)->default(0.2500);      // P(G)

            // Diagnostic Tracking
            $table->boolean('has_taken_diagnostic')->default(false);
            $table->timestamp('diagnostic_completed_at')->nullable();

            // Performance Tracking
            $table->integer('total_questions_answered')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('total_assessments_taken')->default(0);

            // Cumulative Time Score for F-Time Calculation
            $table->decimal('cumulative_time_score', 8, 4)->default(0.0000);
            
            // Current F-Time Factor (for BKT calculations)
            $table->decimal('current_ftime_factor', 5, 4)->default(1.0000);
            $table->decimal('average_time_factor', 5, 4)->default(1.0000);

            // Timestamps
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Unique Constraint: A user can only have one record per competency
            $table->unique(['user_id', 'competency'], 'unique_user_competency');

            // Indexes for Query Optimization
            $table->index('user_id', 'idx_user_mastery');
            $table->index(['competency', 'current_difficulty'], 'idx_competency_difficulty');
            $table->index('final_mastery_score', 'idx_mastery_score');
            $table->index('has_taken_diagnostic', 'idx_has_taken_diagnostic');
            $table->index(['user_id', 'competency', 'has_taken_diagnostic'], 'idx_diagnostic_status');
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
