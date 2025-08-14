<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_gamification', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique();

            $table->integer('total_points')->default(0);
            $table->integer('current_level')->default(1);
            $table->integer('points_to_next_level')->default(35);

            $table->integer('daily_points')->default(0);
            $table->integer('weekly_points')->default(0);
            $table->integer('monthly_points')->default(0);

            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();

            $table->integer('total_assessments_completed')->default(0);
            $table->integer('perfect_assessments_count')->default(0);

            $table->integer('na_assessments_completed')->default(0);
            $table->integer('mg_assessments_completed')->default(0);
            $table->integer('dp_assessments_completed')->default(0);

            $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_gamification');
    }
};
