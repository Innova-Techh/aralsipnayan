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
        Schema::create('quiz_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('session_id', 100);
            $table->string('question_id', 50);
            $table->string('current_answer')->nullable();
            $table->integer('time_taken')->default(0);
            $table->integer('question_index')->default(0);
            $table->timestamp('saved_at')->useCurrent();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['user_id', 'session_id']);
            $table->index(['user_id', 'session_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_progress');
    }
};
