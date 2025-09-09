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
        Schema::create('diagnostic_sessions', function (Blueprint $table) {
            $table->string('session_id', 50)->primary();
            $table->unsignedBigInteger('user_id');
            $table->enum('competency', ['number_algebra', 'measurement_geometry', 'data_probability']);

            // Diagnostic Configuration
            $table->integer('total_phases')->default(3); // beginner, intermediate, advanced
            $table->integer('current_phase')->default(1);

            // Phase Results
            $table->decimal('phase_1_score', 5, 4)->nullable(); 
            $table->integer('phase_1_questions')->default(15);
            $table->decimal('phase_2_score', 5, 4)->nullable();
            $table->integer('phase_2_questions')->default(10);
            $table->decimal('phase_3_score', 5, 4)->nullable();
            $table->integer('phase_3_questions')->default(10);

            // Final Diagnostic Results
            $table->decimal('final_master_score', 5, 2)->nullable();
            $table->enum('recommended_difficulty', ['beginner', 'intermediate', 'advanced'])->nullable();

            // Session Status & Tracking
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();

            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes & Constraints
            $table->unique(['user_id', 'competency'], 'unique_user_competency_diagnostic');
            $table->index(['status', 'started_at'], 'idx_diagnostic_status');
            $table->index(['user_id', 'completed_at'], 'idx_user_diagnostics');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostic_sessions');
    }
};
