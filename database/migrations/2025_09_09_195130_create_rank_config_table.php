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
        Schema::create('rank_config', function (Blueprint $table) {
            $table->id('rank_id');
            $table->string('rank_name', 50)->unique();
            $table->integer('required_level');
            $table->text('rank_description')->nullable();
            $table->string('rank_insignia_url', 500)->nullable();

            // Index for fast queries on level
            $table->index('required_level', 'idx_required_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rank_config');
    }
};
