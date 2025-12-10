<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_assessment_questions', function (Blueprint $table) {

            $table->id(); // auto-increment PK

            $table->uuid('pool_id'); // no longer the PK
            $table->unsignedBigInteger('teacher_assessment_id');
            $table->string('question_id', 50);
            $table->integer('question_order');
            $table->integer('shuffled_order')->nullable(); // added here

            $table->boolean('is_answered')->default(false);
            $table->boolean('is_current')->default(false);

            $table->unsignedBigInteger('created_by');
            $table->timestamp('added_at')->useCurrent();
            $table->timestamps();

            // Foreign keys
            $table->foreign('teacher_assessment_id')
                ->references('id')->on('teacher_assessments')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('cascade');

            // Indexes
            $table->index('pool_id', 'idx_pool_id');
            $table->unique(['teacher_assessment_id', 'question_id'], 'unique_teacher_assessment_question');
            $table->index(['teacher_assessment_id', 'question_order'], 'idx_teacher_assessment_order');
            $table->index(['teacher_assessment_id', 'is_current'], 'idx_teacher_current_question');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_assessment_questions');
    }
};
