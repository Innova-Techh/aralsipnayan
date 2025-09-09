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
        Schema::create('question_media', function (Blueprint $table) {
            // Primary Key
            $table->string('media_id', 50)->primary();

            // Foreign Key Reference to Questions Table
            $table->string('question_id', 50);
            $table->foreign('question_id')->references('question_id')->on('questions')->onDelete('cascade');

            // Media Information
            $table->enum('media_type', ['image', 'video', 'audio'])->default('image');
            $table->string('media_url', 500);
            $table->text('media_description')->nullable();
            $table->integer('display_order')->default(1); // For multiple images per question

            // Metadata
            $table->timestamp('created_at')->useCurrent();

            // Indexes for performance
            $table->index('question_id', 'idx_question_media');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_media');
    }
};
