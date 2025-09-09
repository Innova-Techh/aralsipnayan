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
        Schema::create('points_transactions', function (Blueprint $table) {
            $table->string('transaction_id', 50)->primary();
            $table->unsignedBigInteger('user_id');

            // Points Details
            $table->integer('points_earned');
            $table->enum('points_type', [
                'base_question', 
                'time_bonus', 
                'competency_completion', 
                'perfect_assessment', 
                'daily_triple_completion', 
                'daily_streak'
            ]);

            // Context Information
            $table->string('question_id', 50)->nullable();
            $table->string('assessment_id', 50)->nullable();
            $table->enum('competency', ['number_algebra', 'measurement_geometry', 'data_probability'])->nullable();
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->nullable();

            // Additional Details
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamp('earned_at')->useCurrent();

            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['user_id', 'earned_at'], 'idx_user_points');
            $table->index('points_type', 'idx_points_type');
            $table->index(['competency', 'earned_at'], 'idx_competency_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_transactions');
    }
};
