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
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('assessment_id');
            $table->integer('score');
            $table->integer('total_questions');
            $table->decimal('percentage', 5, 2);
            $table->integer('time_taken'); // in seconds
            $table->json('answers')->nullable(); // store all answers
            $table->timestamp('completed_at');
            $table->timestamps();
            
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assessment_id')->references('id')->on('teacher_assessments')->onDelete('cascade');
            
            $table->unique(['student_id', 'assessment_id']); // one result per student per assessment
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_results');
    }
};
