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
        Schema::create('teacher_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teacher_profile')->onDelete('cascade');
            $table->string('section', 50);
            $table->string('grade_level', 10)->default('6');
            $table->string('school_year', 20)->nullable(); // e.g. 2024-2025
            $table->timestamps();
            
            // Ensure a teacher can't be assigned to the same section twice
            $table->unique(['teacher_id', 'section', 'school_year'], 'unique_teacher_section_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_sections');
    }
};