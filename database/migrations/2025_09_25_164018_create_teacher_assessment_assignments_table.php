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
        Schema::create('teacher_assessment_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('student_id')->nullable(); // null means assigned to entire section
            $table->string('section')->nullable(); // section name
            $table->boolean('accommodations')->default(false);
            $table->enum('status', ['Assigned', 'In Progress', 'Completed', 'Overdue'])->default('Assigned');
            $table->datetime('assigned_at');
            $table->datetime('due_date')->nullable();
            $table->timestamps();
            
            $table->foreign('assessment_id')->references('id')->on('teacher_assessments')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['assessment_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_assessment_assignments');
    }
};
