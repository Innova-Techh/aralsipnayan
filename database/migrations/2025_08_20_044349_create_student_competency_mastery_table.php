<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_competency_mastery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Competency categories
            $table->enum('competency', ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability']);

            // Difficulty tracking
            $table->enum('current_difficulty_level', ['Beginner', 'Intermediate', 'Advanced'])->default('Beginner');

            // BKT probability (0.000 - 1.000)
            $table->decimal('mastery_probability', 4, 3)->default(0.300);

            // Performance statistics
            $table->integer('total_questions_answered')->default(0);
            $table->integer('correct_answers')->default(0);

            // Placeholder column for accuracy_rate (calculated via raw SQL later)
            $table->decimal('accuracy_rate', 4, 3)->default(0);

            // BKT parameters
            $table->decimal('p_learn', 4, 3)->default(0.100);
            $table->decimal('p_guess', 4, 3)->default(0.250);
            $table->decimal('p_slip', 4, 3)->default(0.100);

            // Diagnostic status tracking
            $table->boolean('diagnostic_completed')->default(false);
            $table->dateTime('diagnostic_date')->nullable();

            // Assessment timestamps
            $table->dateTime('last_assessment_date')->nullable();
            $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate();

            // Unique key per user + competency
            $table->unique(['user_id', 'competency']);
        });

        // Add the generated column for accuracy_rate after creating the table
        DB::statement('ALTER TABLE student_competency_mastery 
            MODIFY accuracy_rate DECIMAL(4,3) 
            GENERATED ALWAYS AS (
                CASE WHEN total_questions_answered > 0 
                THEN correct_answers / total_questions_answered 
                ELSE 0 END
            ) STORED');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_competency_mastery');
    }
};
