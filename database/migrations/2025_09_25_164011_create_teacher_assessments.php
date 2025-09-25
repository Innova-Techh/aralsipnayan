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
        Schema::create('teacher_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['Number & Algebra', 'Measurement & Geometry', 'Data & Probability']);
            $table->integer('number_of_questions');
            $table->integer('time_limit'); // in minutes
            $table->enum('difficulty', ['Easy', 'Medium', 'Hard', 'Mixed']);
            $table->enum('status', ['Draft', 'Active', 'Completed', 'Archived'])->default('Draft');
            $table->boolean('is_live_quiz')->default(false);
            $table->datetime('available_from')->nullable();
            $table->datetime('available_until')->nullable();
            $table->unsignedBigInteger('created_by'); // teacher_id
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_assessments');
    }
};
