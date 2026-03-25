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
        Schema::create('radm_detection_labels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('detection_id')->unique();

            // Ground-truth label: true means actually random-answering behavior was observed.
            $table->boolean('actual_random_answering');

            // Optional metadata for traceability.
            $table->string('label_source', 50)->default('manual');
            $table->unsignedBigInteger('labeled_by')->nullable();
            $table->timestamp('labeled_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('detection_id')
                ->references('id')
                ->on('radm_detections')
                ->onDelete('cascade');

            $table->foreign('labeled_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index('actual_random_answering');
            $table->index('label_source');
            $table->index('labeled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radm_detection_labels');
    }
};
