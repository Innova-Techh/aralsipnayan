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
        Schema::create('teacher_quiz_responses', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->string('session_id', 50);
            $table->uuid('pool_id');
            $table->string('question_id', 50);
            $table->unsignedBigInteger('student_id');
            
            // Response Data
            $table->string('student_answer');
            $table->string('correct_answer');
            $table->boolean('is_correct');
            $table->integer('points_earned')->default(0);
            $table->integer('time_taken'); // in seconds
            
            // BKT Tracking
            $table->decimal('bkt_probability_before', 5, 4)->nullable();
            $table->decimal('bkt_probability_after', 5, 4)->nullable();
            
            // Question Metadata
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->nullable();
            $table->string('competency', 50)->nullable();
            $table->string('topic_tag')->nullable();
            
            // Timestamps
            $table->timestamp('answered_at')->useCurrent();
            $table->timestamps();
            
            // Foreign Key Constraints
            $table->foreign('session_id')
                  ->references('session_id')
                  ->on('teacher_assessment_sessions')
                  ->onDelete('cascade');
                  
            $table->foreign('pool_id')
                  ->references('pool_id')
                  ->on('teacher_assessment_questions')
                  ->onDelete('cascade');
                  
            $table->foreign('student_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index(['session_id', 'question_id'], 'idx_session_question');
            $table->index(['student_id', 'is_correct'], 'idx_student_correctness');
            $table->index(['pool_id', 'student_id'], 'idx_pool_student');
            $table->index(['difficulty_level', 'is_correct'], 'idx_difficulty_performance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_quiz_responses');
    }
};
