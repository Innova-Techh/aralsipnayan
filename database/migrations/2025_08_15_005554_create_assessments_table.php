<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->string('assessment_id', 30)->primary();
            $table->string('assessment_name', 200);
            $table->enum('assessment_type', ['Diagnostic', 'Adaptive_Builtin', 'Custom_Teacher']);
            $table->enum('competency', ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability', 'Mixed']);
            $table->enum('difficulty_level', ['Beginner', 'Intermediate', 'Advanced', 'Mixed'])->default('Mixed');

            $table->integer('time_limit_minutes')->nullable();
            $table->integer('total_questions');
            $table->integer('total_points')->default(0);

            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->boolean('is_active')->default(true);

            $table->boolean('is_adaptive')->default(true);

            $table->timestamps();

            $table->index(['assessment_type', 'competency'], 'idx_assessment_type');
            $table->index(['is_active', 'competency'], 'idx_active_assessments');

            $table->foreign('created_by_user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
