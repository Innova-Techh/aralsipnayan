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
        Schema::create('radm_detections', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 50);
            $table->unsignedBigInteger('student_id');
            $table->string('assessment_id', 50)->nullable();
            
            // Evaluation window data
            $table->integer('window_start_index');
            $table->integer('window_end_index');
            $table->json('question_ids'); // Array of 5 question IDs in window
            
            // Time Behavior Indicator (T)
            $table->decimal('avg_response_time', 8, 2);
            $table->decimal('expected_time', 8, 2);
            $table->decimal('time_threshold', 8, 2);
            $table->boolean('time_flag'); // T indicator (0 or 1)
            
            // Accuracy Indicator (A)
            $table->integer('correct_count');
            $table->integer('total_count'); // Should be 5
            $table->decimal('accuracy', 5, 4);
            $table->boolean('accuracy_flag'); // A indicator (0 or 1)
            
            // BKT Contradiction Indicator (B)
            $table->decimal('bkt_probability', 5, 4)->nullable();
            $table->integer('consecutive_wrong')->default(0);
            $table->boolean('bkt_flag'); // B indicator (0 or 1)
            
            // Random Answering Index (RAI)
            $table->decimal('rai_score', 5, 4);
            $table->boolean('intervention_triggered');
            
            // Intervention response
            $table->boolean('intervention_acknowledged')->default(false);
            $table->timestamp('acknowledged_at')->nullable();
            
            // Metadata
            $table->string('difficulty_level'); // beginner, intermediate, advanced
            $table->json('response_times'); // Array of 5 response times
            $table->json('correctness'); // Array of 5 boolean values
            
            $table->timestamps();
            
            // Indexes
            $table->index('session_id');
            $table->index('student_id');
            $table->index('intervention_triggered');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radm_detections');
    }
};
