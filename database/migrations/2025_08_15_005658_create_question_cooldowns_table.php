<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('question_cooldowns', function (Blueprint $table) {
            $table->bigIncrements('cooldown_id');
            $table->unsignedBigInteger('user_id');
            $table->string('question_id', 20);
            $table->enum('competency', ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability']);

            $table->dateTime('cooldown_until');
            $table->enum('cooldown_reason', ['correct_answer', 'wrong_answer']);
            $table->integer('cooldown_hours');

            $table->string('session_id', 40);
            $table->boolean('is_active')->default(true);

            $table->timestamp('created_at')->useCurrent();

            $table->unique(['user_id', 'question_id', 'is_active'], 'unique_user_question_active');
            $table->index(['user_id', 'competency', 'cooldown_until'], 'idx_user_competency_cooldown');
            $table->index(['is_active', 'cooldown_until'], 'idx_active_cooldowns');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('question_id')->references('question_id')->on('questions')->onDelete('cascade');
            $table->foreign('session_id')->references('session_id')->on('assessment_sessions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_cooldowns');
    }
};
