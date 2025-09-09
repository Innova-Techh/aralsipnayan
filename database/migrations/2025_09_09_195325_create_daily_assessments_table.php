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
        Schema::create('daily_assessments', function (Blueprint $table) {
            $table->string('record_id', 50)->primary();
            $table->unsignedBigInteger('user_id');
            $table->date('assessment_date');

            // Daily Progress
            $table->integer('assessments_completed')->default(0);
            $table->json('competencies_completed')->nullable(); // replaced SET with JSON

            // Points earned today
            $table->integer('points_earned_today')->default(0);

            // Streak tracking
            $table->boolean('contributes_to_streak')->default(false);

            // Metadata
            $table->timestamps(); // created_at & updated_at

            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Constraints & Indexes
            $table->unique(['user_id', 'assessment_date'], 'unique_user_date');
            $table->index('assessment_date', 'idx_assessment_date');
            $table->index(['user_id', 'assessment_date'], 'idx_streak_tracking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_assessments');
    }
};
