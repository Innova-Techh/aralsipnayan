<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('question_cooldowns', function (Blueprint $table) {
            // Primary Key
            $table->string('cooldown_id', 50)->primary();

            // Foreign Keys
            $table->unsignedBigInteger('user_id');
            $table->string('question_id', 50);

            // Competency
            $table->enum('competency', ['number_algebra', 'measurement_geometry', 'data_probability']);

            // Answer Details
            $table->boolean('was_correct');
            $table->timestamp('answered_at');
            $table->decimal('response_time', 8, 3); // Seconds with millisecond precision

            // Cooldown Settings
            $table->integer('cooldown_duration'); // Minutes (60 for correct, 30 for incorrect)
            $table->timestamp('cooldown_until')->nullable(); // fixed
            $table->boolean('is_active')->default(true);

            // Metadata
            $table->timestamp('created_at')->useCurrent();

            // Foreign Key Constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('question_id')->references('question_id')->on('questions')->onDelete('cascade');

            // Composite Index for Efficient Cooldown Queries
            $table->index(['user_id', 'competency', 'is_active', 'cooldown_until'], 'idx_user_competency_cooldown');
            $table->index(['cooldown_until', 'is_active'], 'idx_cooldown_expiry');

            // Prevent Duplicate Active Cooldowns for Same User-Question Pair
            $table->unique(['user_id', 'question_id', 'is_active'], 'unique_active_cooldown');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_cooldowns');
    }
};
