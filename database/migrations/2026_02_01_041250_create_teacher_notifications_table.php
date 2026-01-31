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
        Schema::create('teacher_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teacher_profile')->onDelete('cascade');
            $table->string('type', 50); // 'profile_update', 'password_change', 'photo_update'
            $table->string('action', 100); // Description of the action
            $table->text('details')->nullable(); // JSON or text details of the change
            $table->string('ip_address', 45)->nullable(); // IP address of the user
            $table->string('user_agent')->nullable(); // Browser/device information
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['teacher_id', 'created_at']);
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_notifications');
    }
};
