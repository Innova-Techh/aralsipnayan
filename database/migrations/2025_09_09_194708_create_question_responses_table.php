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
        Schema::create('question_responses', function (Blueprint $table) {
            $table->string('response_id', 50)->primary();
            $table->string('assessment_id', 50);
            $table->unsignedBigInteger('user_id');
            $table->string('question_id', 50);

            // Response Details
            $table->text('user_answer')->nullable();
            $table->boolean('is_correct');
            $table->decimal('response_time', 8, 3); // seconds with milliseconds

            // Timing Analysis
            $table->integer('max_allowed_time');
            $table->decimal('normalized_time', 5, 4)->nullable();
            $table->decimal('time_score', 3, 2)->nullable();

            // BKT Calculation Components
            $table->decimal('bkt_before', 5, 4);
            $table->decimal('bkt_after', 5, 4);

            // Difficulty and Time Factors
            $table->decimal('difficulty_factor', 3, 2)->nullable();
            $table->decimal('time_factor', 5, 4)->nullable();

            // Points Earned
            $table->integer('base_points')->default(0);
            $table->integer('time_bonus_points')->default(0);
            $table->integer('total_points')->default(0);

            $table->timestamp('answered_at')->useCurrent();

            // Foreign Keys
            $table->foreign('assessment_id')->references('assessment_id')->on('assessments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('question_id')->references('question_id')->on('questions');

            // Indexes
            $table->index(['assessment_id', 'answered_at'], 'idx_assessment_responses');
            $table->index(['user_id', 'answered_at'], 'idx_user_responses');
            $table->index(['question_id', 'is_correct'], 'idx_question_performance');
            $table->index(['user_id', 'answered_at'], 'idx_bkt_tracking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_responses');
    }
};
