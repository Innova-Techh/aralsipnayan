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
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->string('pool_id', 50)->primary();
            $table->string('assessment_id', 50);
            $table->string('question_id', 50);

            // Question Order (after Fisher-Yates shuffling)
            $table->integer('question_order');

            // Question Status
            $table->boolean('is_answered')->default(false);
            $table->boolean('is_current')->default(false); // currently active question

            $table->timestamp('added_at')->useCurrent();

            // Foreign Keys
            $table->foreign('assessment_id')->references('assessment_id')->on('assessments')->onDelete('cascade');
            $table->foreign('question_id')->references('question_id')->on('questions');

            // Constraints & Indexes
            $table->unique(['assessment_id', 'question_id'], 'unique_assessment_question');
            $table->index(['assessment_id', 'question_order'], 'idx_assessment_order');
            $table->index(['assessment_id', 'is_current'], 'idx_current_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_questions');
    }
};
