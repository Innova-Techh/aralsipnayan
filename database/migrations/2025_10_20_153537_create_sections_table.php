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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('grade_level', 10)->default('6');
            $table->string('school_year', 20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Ensure unique section names per school year
            $table->unique(['name', 'school_year'], 'unique_section_school_year');
            
            // Add index for faster queries
            $table->index(['school_year', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};