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
        // Add missing fields for enhanced mastery calculations
        Schema::table('assessments', function (Blueprint $table) {
            // Add BKT component fields
            $table->decimal('bkt_final_score', 5, 4)->nullable()->after('bkt_score_after');
            $table->decimal('accuracy_component', 5, 4)->nullable()->after('accuracy_percentage');
            $table->decimal('bkt_component', 5, 4)->nullable()->after('bkt_final_score');
            
            // Add time-related scores
            $table->decimal('cumulative_time_score', 8, 4)->default(0.0000)->after('time_performance_score');
            $table->decimal('average_time_factor', 5, 4)->nullable()->after('cumulative_time_score');
        });

        Schema::table('diagnostic_sessions', function (Blueprint $table) {
            // Add weighted scores for final calculation
            $table->decimal('accuracy_component', 5, 4)->nullable()->after('final_master_score');
            $table->decimal('bkt_component', 5, 4)->nullable()->after('accuracy_component');
            
            // Add time factors per phase
            $table->decimal('phase_1_time_factor', 5, 4)->nullable()->after('phase_1_questions');
            $table->decimal('phase_2_time_factor', 5, 4)->nullable()->after('phase_2_questions');
            $table->decimal('phase_3_time_factor', 5, 4)->nullable()->after('phase_3_questions');
        });

        Schema::table('student_mastery', function (Blueprint $table) {
            // Add component tracking for transparency
            $table->decimal('accuracy_component', 5, 4)->nullable()->after('accuracy_score');
            $table->decimal('bkt_component', 5, 4)->nullable()->after('bkt_score');
            
            // Add average time factor tracking
            $table->decimal('average_time_factor', 5, 4)->default(1.0000)->after('current_ftime_factor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn(['bkt_final_score', 'accuracy_component', 'bkt_component', 'cumulative_time_score', 'average_time_factor']);
        });

        Schema::table('diagnostic_sessions', function (Blueprint $table) {
            $table->dropColumn(['accuracy_component', 'bkt_component', 'phase_1_time_factor', 'phase_2_time_factor', 'phase_3_time_factor']);
        });

        Schema::table('student_mastery', function (Blueprint $table) {
            $table->dropColumn(['accuracy_component', 'bkt_component', 'average_time_factor']);
        });
    }
};