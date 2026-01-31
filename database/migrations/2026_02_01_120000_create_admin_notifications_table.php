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
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admin_profile')->onDelete('cascade');
            $table->string('type', 50); // e.g., profile_update, password_change, admin_created, admin_updated, etc.
            $table->string('action', 255); // Description of the action
            $table->json('details')->nullable(); // Additional details in JSON format
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('admin_id');
            $table->index('is_read');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
