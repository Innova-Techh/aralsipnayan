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
        Schema::create('student_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');

            // Student Information
            $table->string('student_id', 50)->unique()->nullable(); // Official student ID/LRN
            $table->string('firstname', 100);
            $table->string('lastname', 100);
            $table->string('middlename', 100)->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('section', 50)->nullable;
            $table->string('grade_level', 10)->default('6');
            $table->string('school_name', 150)->default('Pembo Elementary School');
            $table->string('school_year', 20)->nullable(); // e.g. 2024-2025

            // Avatar field - ADDED THIS
            $table->string('avatar_url', 255)->default('/images/profile/default.png');

            // Onboarding Tracking
            $table->boolean('has_completed_onboarding')->default(false);
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->boolean('is_first_login')->default(true);

            // First Assessment Tracking
            $table->boolean('has_viewed_assessments')->default(false);
            $table->timestamp('first_assessment_view_at')->nullable();

            // Gamification Fields
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->integer('total_points')->default(0);

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profile');
    }
};