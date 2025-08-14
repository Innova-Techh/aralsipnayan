<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('question_responses', function (Blueprint $table) {
            $table->bigIncrements('response_id');
            $table->string('session_id', 40);
            $table->string('question_id', 20);
            $table->integer('question_order');

            $table->text('student_answer');
            $table->boolean('is_correct');
            $table->integer('points_earned')->default(0);
            $table->integer('response_time_seconds')->nullable();

            $table->decimal('mastery_before', 4, 3)->nullable();
            $table->decimal('mastery_after', 4, 3)->nullable();

            $table->boolean('hint_used')->default(false);
            $table->boolean('hint_penalty_applied')->default(false);

            $table->timestamp('answered_at')->useCurrent();

            $table->index(['session_id', 'question_order'], 'idx_session_order');
            $table->index(['question_id', 'is_correct'], 'idx_question_performance');
            $table->index(['session_id', 'answered_at'], 'idx_user_responses');

            $table->foreign('session_id')->references('session_id')->on('assessment_sessions')->onDelete('cascade');
            $table->foreign('question_id')->references('question_id')->on('questions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_responses');
    }
};
