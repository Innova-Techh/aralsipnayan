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
        Schema::create('bkt_parameters', function (Blueprint $table) {
            // Primary Key
            $table->id('config_id');

            // Competency & Difficulty
            $table->enum('competency', ['number_algebra', 'measurement_geometry', 'data_probability']);
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced']);

            // Standard BKT Parameters
            $table->decimal('initial_knowledge', 5, 4)->default(0.1000); // P(L0)
            $table->decimal('learn_rate', 5, 4)->default(0.3000);        // P(T)
            $table->decimal('slip_rate', 5, 4)->default(0.1000);         // P(S)
            $table->decimal('guess_rate', 5, 4)->default(0.2500);        // P(G)

            // Difficulty Weight (affects scoring)
            $table->decimal('difficulty_weight', 3, 2); // e.g., 0.8 / 1.0 / 1.2

            // Time Scoring Configuration
            $table->decimal('fast_threshold', 3, 2)->default(0.50);  // ≤0.5 normalized time = fast
            $table->decimal('medium_threshold', 3, 2)->default(0.80); // ≤0.8 normalized time = medium

            // Time Score Values
            $table->decimal('fast_correct_score', 3, 2)->default(1.00);
            $table->decimal('medium_correct_score', 3, 2)->default(0.80);
            $table->decimal('slow_correct_score', 3, 2)->default(0.60);
            $table->decimal('fast_incorrect_score', 3, 2)->default(0.20);
            $table->decimal('slow_incorrect_score', 3, 2)->default(0.10);
            $table->decimal('timeout_score', 3, 2)->default(0.00);

            // Mastery Score Weights
            $table->decimal('accuracy_weight', 3, 2)->default(0.55); // 55%
            $table->decimal('bkt_weight', 3, 2)->default(0.45);      // 45%

            // Status & Timestamps
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();

            // Unique Constraint: One Config per Competency + Difficulty
            $table->unique(['competency', 'difficulty_level'], 'unique_competency_difficulty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bkt_parameters');
    }
};
