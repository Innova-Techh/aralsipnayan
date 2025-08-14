<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessment_sessions', function (Blueprint $table) {
            $table->string('session_id', 40)->primary();
            $table->unsignedBigInteger('user_id');
            $table->string('assessment_id', 30)->nullable();
            $table->enum('session_type', ['Diagnostic', 'Adaptive', 'Custom', 'Practice']);
            $table->enum('competency', ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability', 'Mixed']);
            $table->enum('difficulty_level', ['Beginner', 'Intermediate', 'Advanced', 'Mixed']);

            $table->json('questions_json');
            $table->integer('total_questions');

            $table->dateTime('start_time')->useCurrent();
            $table->dateTime('end_time')->nullable();
            $table->integer('time_limit_minutes')->nullable();

            $table->integer('questions_answered')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('total_points_earned')->default(0);
            $table->decimal('accuracy_rate', 4, 3)->default(0.000);

            $table->enum('status', ['in_progress', 'completed', 'time_expired', 'abandoned'])->default('in_progress');

            $table->decimal('initial_mastery_probability', 4, 3)->nullable();
            $table->decimal('final_mastery_probability', 4, 3)->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status'], 'idx_user_sessions');
            $table->index(['competency', 'session_type'], 'idx_session_competency');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assessment_id')->references('assessment_id')->on('assessments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_sessions');
    }
};
