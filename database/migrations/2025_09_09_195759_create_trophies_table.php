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
        Schema::create('trophies', function (Blueprint $table) {
            $table->string('trophy_id', 50)->primary();
            $table->string('trophy_name', 100);
            $table->enum('trophy_type', ['class_weekly', 'school_monthly']);
            $table->text('trophy_description')->nullable();

            // Trophy Configuration
            $table->integer('max_winners');
            $table->enum('reset_frequency', ['weekly', 'monthly']);

            $table->string('trophy_icon_url', 500)->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trophies');
    }
};
