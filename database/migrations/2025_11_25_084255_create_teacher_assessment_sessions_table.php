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
        Schema::create('teacher_assessment_sessions', function (Blueprint $table) {
            // Primary Key
            $table->string('session_id', 50)->primary();

            // Foreign Key References
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('teacher_assessment_id');

            // Session Type & Competency
            $table->enum('session_type', ['teacher_created', 'practice'])->default('teacher_created');
            $table->enum('competency', ['number_algebra', 'measurement_geometry', 'data_probability', 'mixed']);
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'mixed']);

            // Assessment Configuration
            $table->integer('total_questions');
            $table->json('questions_json'); // Array of selected question IDs in shuffled order
            $table->integer('current_question_index')->default(0); // Track progress through questions

            // Timer Configuration & Management
            $table->integer('time_limit_minutes')->nullable(); // Overall session time limit
            $table->integer('total_time_allowed_seconds')->nullable(); // Calculated from time_limit_minutes
            $table->timestamp('countdown_started_at')->nullable(); // When timer actually started
            $table->timestamp('countdown_expires_at')->nullable(); // When session should auto-expire
            $table->integer('time_remaining_seconds')->nullable(); // Real-time remaining time
            $table->boolean('auto_submit_on_timeout')->default(true);

            // Per-Question Time Limits (Based on difficulty - 30/45/60 seconds)
            $table->integer('beginner_question_time_limit')->default(30);
            $table->integer('intermediate_question_time_limit')->default(45);
            $table->integer('advanced_question_time_limit')->default(60);
            $table->integer('current_question_time_limit')->nullable(); // Active question's time limit

            // Pause/Resume Functionality
            $table->timestamp('paused_at')->nullable();
            $table->integer('total_paused_time_seconds')->default(0);
            $table->boolean('is_paused')->default(false);

            // Progress & Performance Tracking
            $table->integer('questions_answered')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('incorrect_answers')->default(0);
            $table->integer('total_points_earned')->default(0);

            // Calculated Performance Metrics
            $table->decimal('accuracy_percentage', 5, 2)->nullable(); // (correct/total) * 100
            $table->decimal('accuracy_component', 5, 4)->nullable(); // Weighted accuracy for final score
            $table->decimal('average_response_time', 8, 3)->nullable(); // Average seconds per question
            $table->decimal('time_performance_score', 5, 4)->nullable(); // Overall time factor
            $table->decimal('cumulative_time_score', 8, 4)->default(0.0000); // Sum of individual time scores

            // BKT (Bayesian Knowledge Tracing) Components
            $table->decimal('initial_bkt_probability', 5, 4)->nullable(); // Starting mastery probability
            $table->decimal('final_bkt_probability', 5, 4)->nullable(); // Ending mastery probability
            $table->decimal('bkt_component', 5, 4)->nullable(); // BKT weight in final score
            $table->decimal('final_mastery_score', 5, 2)->nullable(); // Combined accuracy + BKT score

            // Session Status & Timestamps
            $table->enum('status', ['in_progress', 'completed', 'time_expired', 'abandoned'])->default('in_progress');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_activity_at')->useCurrent()->useCurrentOnUpdate();

            // Additional Metadata
            $table->text('session_notes')->nullable();
            $table->json('session_metadata')->nullable();
            
            // Foreign Key Constraints
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('teacher_assessment_id')
                  ->references('id')
                  ->on('teacher_assessments')
                  ->onDelete('cascade');

            // Indexes for Performance Optimization
            $table->index(['user_id', 'session_type'], 'idx_user_session_type');
            $table->index(['competency', 'difficulty_level'], 'idx_competency_difficulty');
            $table->index(['status', 'started_at'], 'idx_status_timeline');
            $table->index(['user_id', 'competency', 'status'], 'idx_user_competency_status');
            
            // Timer-specific indexes
            $table->index(['countdown_expires_at', 'status'], 'idx_countdown_expiration');
            $table->index(['is_paused', 'user_id'], 'idx_paused_sessions');
            $table->index(['countdown_started_at', 'status'], 'idx_active_countdowns');
            
            // Performance analysis indexes
            $table->index(['final_mastery_score', 'competency'], 'idx_mastery_competency');
            $table->index(['accuracy_percentage', 'difficulty_level'], 'idx_accuracy_difficulty');
            $table->index(['session_type', 'completed_at'], 'idx_session_completion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_assessment_sessions', function (Blueprint $table) {
            // Drop custom indexes first
            $table->dropIndex('idx_countdown_expiration');
            $table->dropIndex('idx_paused_sessions');
            $table->dropIndex('idx_active_countdowns');
            $table->dropIndex('idx_mastery_competency');
            $table->dropIndex('idx_accuracy_difficulty');
            $table->dropIndex('idx_session_completion');
        });

        Schema::dropIfExists('teacher_assessment_sessions');
    }
};
