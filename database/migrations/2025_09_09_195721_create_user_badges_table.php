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
        Schema::create('user_badges', function (Blueprint $table) {
            $table->string('user_badge_id', 50)->primary();
            $table->unsignedBigInteger('user_id');
            $table->string('badge_id', 50);

            $table->timestamp('earned_at')->useCurrent();
            $table->boolean('notification_sent')->default(false);

            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('badge_id')->references('badge_id')->on('badge_config');

            // Unique & Indexes
            $table->unique(['user_id', 'badge_id'], 'unique_user_badge');
            $table->index(['user_id', 'earned_at'], 'idx_user_badges');
            $table->index(['badge_id', 'earned_at'], 'idx_badge_earnings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};
