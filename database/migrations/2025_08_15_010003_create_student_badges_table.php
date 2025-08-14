<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_badges', function (Blueprint $table) {
            $table->bigIncrements('badge_id');
            $table->unsignedBigInteger('user_id');

            $table->string('badge_name', 100);
            $table->enum('badge_type', ['Milestone', 'Competency', 'Achievement', 'Special']);
            $table->text('badge_description')->nullable();
            $table->string('badge_icon_url', 255)->nullable();

            $table->integer('points_requirement')->nullable();
            $table->enum('competency', ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability'])->nullable();
            $table->text('special_condition')->nullable();

            $table->timestamp('earned_at')->useCurrent();

            $table->index(['user_id', 'badge_type'], 'idx_user_badges');
            $table->index(['earned_at', 'badge_type'], 'idx_badge_earned');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_badges');
    }
};
