<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->string('assessment_id', 50)->primary();
            $table->unsignedBigInteger('user_id');
            $table->enum('competency', ['number_algebra', 'measurement_geometry', 'data_probability']);
            
            // Assessment Type & Configuration
            $table->enum('assessment_type', ['diagnostic', 'regular']);
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced']);
            $table->integer('total_questions');
            
            // Assessment Status
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            
            // Diagnostic-specific fields
            $table->boolean('is_diagnostic_phase')->default(false);
            $table->integer('diagnostic_phase')->nullable(); // 1=beginner, 2=intermediate, 3=advanced
            
            // Scoring & Performance
            $table->integer('correct_answers')->default(0);
            $table->integer('incorrect_answers')->default(0);
            $table->integer('questions_answered')->default(0);
            
            // Calculated Scores (after completion)
            $table->decimal('accuracy_percentage', 5, 2)->nullable();
            $table->decimal('bkt_score_before', 5, 4)->nullable();
            $table->decimal('bkt_score_after', 5, 4)->nullable();
            $table->decimal('final_mastery_score', 5, 2)->nullable();
            
            // Time Tracking
            $table->integer('total_time_spent')->default(0);
            $table->decimal('average_response_time', 8, 3)->nullable();
            $table->decimal('time_performance_score', 5, 4)->nullable();
            
            // Session Management
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_activity')->useCurrent()->useCurrentOnUpdate();
            
            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['user_id', 'started_at'], 'idx_user_assessments');
            $table->index(['competency', 'assessment_type'], 'idx_competency_assessments');
            $table->index(['status', 'started_at'], 'idx_assessment_status');
            $table->index(['user_id', 'assessment_type', 'competency'], 'idx_diagnostic_tracking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
