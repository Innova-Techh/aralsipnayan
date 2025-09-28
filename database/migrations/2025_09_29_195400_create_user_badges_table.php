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
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('badge_key', 50);
            $table->string('badge_name', 100);
            $table->string('badge_icon', 10);
            $table->text('badge_description');
            $table->integer('points_required');
            $table->timestamp('awarded_at');
            $table->timestamps();
            
            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate badges
            $table->unique(['user_id', 'badge_key'], 'unique_user_badge');
            
            // Indexes
            $table->index(['user_id', 'awarded_at'], 'idx_user_badges');
            $table->index(['badge_key'], 'idx_badge_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};