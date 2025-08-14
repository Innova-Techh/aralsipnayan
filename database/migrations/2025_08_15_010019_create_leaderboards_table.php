<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->bigIncrements('leaderboard_id');
            $table->enum('leaderboard_type', ['Weekly_Points', 'Monthly_Points', 'Weekly_Streak']);
            $table->date('period_start');
            $table->date('period_end');

            $table->json('rankings_json');
            $table->enum('status', ['active', 'completed', 'archived'])->default('active');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['leaderboard_type', 'period_start', 'period_end'], 'idx_leaderboard_period');
            $table->index(['status', 'leaderboard_type'], 'idx_active_leaderboards');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboards');
    }
};
