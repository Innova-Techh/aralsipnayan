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
        Schema::create('badge_config', function (Blueprint $table) {
            $table->string('badge_id', 50)->primary();
            $table->string('badge_name', 100);
            $table->text('badge_description')->nullable();
            $table->enum('badge_type', ['common', 'uncommon', 'epic', 'legendary', 'rare'])->default('common');

            // Badge Requirements
            $table->integer('points_required')->nullable();
            $table->integer('assessments_required')->nullable();
            $table->string('special_condition', 200)->nullable();

            $table->string('badge_icon_url', 500)->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_config');
    }
};
