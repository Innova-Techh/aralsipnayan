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
        Schema::create('user_progress', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();

            // Total Points and Level
            $table->integer('total_points')->default(0);
            $table->integer('current_level')->default(1);
            $table->integer('points_in_current_level')->default(0);

            // Current Rank
            $table->string('current_rank', 50)->default('Math Explorer');
            $table->integer('rank_level_threshold')->default(10);

            // Daily Streak
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_assessment_date')->nullable();

            // Competency Completion Tracking (JSON instead of SET)
            $table->json('daily_competencies_completed')->nullable();
            $table->date('last_daily_reset')->nullable();

            // Metadata
            $table->timestamps(); // created_at & updated_at

            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['total_points'], 'idx_total_points');
            $table->index(['current_level'], 'idx_current_level');
            $table->index(['current_streak'], 'idx_streak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
