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
        Schema::create('trophy_winners', function (Blueprint $table) {
            $table->string('winner_id', 50)->primary();
            $table->string('period_id', 50);
            $table->unsignedBigInteger('user_id');

            // Winner Details
            $table->integer('final_position');
            $table->integer('points_earned_in_period');

            // Trophy Award Details
            $table->timestamp('awarded_at')->useCurrent();
            $table->boolean('notification_sent')->default(false);

            // Foreign Keys
            $table->foreign('period_id')->references('period_id')->on('trophy_periods');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Constraints & Indexes
            $table->unique(['period_id', 'user_id'], 'unique_period_user');
            $table->index(['period_id', 'final_position'], 'idx_period_rankings');
            $table->index(['user_id', 'awarded_at'], 'idx_user_trophies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trophy_winners');
    }
};
