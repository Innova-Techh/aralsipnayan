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
        Schema::create('student_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('firstname', 100);
            $table->string('lastname', 100);
            $table->string('section', 50)->nullable();
            $table->string('grade_level', 10)->default('6');
            $table->string('school_name', 150)->default('Pembo Elementary School');
            $table->string('avatar_url', 255)->default('/avatars/default.png');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profile');
    }
};
