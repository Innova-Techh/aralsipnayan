<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->string('question_id', 20)->primary();
            $table->enum('competency', ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability']);
            $table->enum('difficulty_level', ['Beginner', 'Intermediate', 'Advanced']);
            $table->enum('question_type', ['Multiple_Choice', 'Fill_Blanks', 'True_False', 'Drag_Drop', 'Connect_Dots'])
                  ->default('Multiple_Choice');
            $table->text('question_text');

            $table->string('choice_a', 255)->nullable();
            $table->string('choice_b', 255)->nullable();
            $table->string('choice_c', 255)->nullable();
            $table->string('choice_d', 255)->nullable();

            $table->text('correct_answer');
            $table->text('hint_text')->nullable();
            $table->text('explanation')->nullable();
            $table->string('topic_tag', 100)->nullable();
            $table->integer('points')->nullable();

            $table->enum('created_by', ['System', 'Teacher'])->default('System');
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['competency', 'difficulty_level'], 'idx_competency_difficulty');
            $table->index('question_type', 'idx_question_type');
            $table->index(['is_active', 'competency', 'difficulty_level'], 'idx_active_questions');

            $table->foreign('created_by_user_id')
                  ->references('id')->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
