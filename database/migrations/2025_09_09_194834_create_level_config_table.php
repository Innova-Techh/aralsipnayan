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
        Schema::create('level_config', function (Blueprint $table) {
            $table->integer('level')->primary();
            $table->integer('points_required'); // cumulative points to reach this level
            $table->integer('points_for_this_level')->default(60); // points needed from previous level
            // Index for faster queries
            $table->index('points_required', 'idx_points_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_config');
    }
};
