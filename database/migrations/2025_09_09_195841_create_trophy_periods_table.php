<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trophy_periods', function (Blueprint $table) {
            $table->string('period_id', 50)->primary();
            $table->string('trophy_id', 50);

            // Period Definition
            $table->timestamp('period_start');
            $table->timestamp('period_end')->nullable(); // fixed
            $table->enum('period_type', ['weekly', 'monthly']);

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('winners_calculated')->default(false);

            // Reference Information
            $table->string('class_id', 50)->nullable();
            $table->string('school_id', 50)->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Foreign Key
            $table->foreign('trophy_id')->references('trophy_id')->on('trophies');

            // Indexes
            $table->index(['is_active', 'period_end'], 'idx_active_periods');
            $table->index(['trophy_id', 'period_start'], 'idx_trophy_periods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trophy_periods');
    }
};
