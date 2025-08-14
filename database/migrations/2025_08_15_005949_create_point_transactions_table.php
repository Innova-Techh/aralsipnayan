<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->bigIncrements('transaction_id');
            $table->unsignedBigInteger('user_id');
            $table->string('session_id', 40)->nullable();

            $table->integer('points_earned');
            $table->enum('transaction_type', [
                'question_correct', 
                'assessment_completion', 
                'perfect_assessment', 
                'daily_bonus', 
                'competency_bonus',
                'streak_bonus'
            ]);

            $table->enum('competency', ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability'])->nullable();
            $table->enum('difficulty_level', ['Beginner', 'Intermediate', 'Advanced'])->nullable();
            $table->text('description')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at'], 'idx_user_transactions');
            $table->index(['transaction_type', 'competency'], 'idx_transaction_type');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('session_id')->references('session_id')->on('assessment_sessions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
