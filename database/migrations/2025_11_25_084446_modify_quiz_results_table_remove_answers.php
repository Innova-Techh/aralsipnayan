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
        Schema::table('quiz_results', function (Blueprint $table) {
            // Drop the answers JSON column
            $table->dropColumn('answers');
            
            // Add session_id to link to teacher_assessment_sessions
            $table->string('session_id', 50)->nullable()->after('assessment_id');
            
            // Add foreign key constraint
            $table->foreign('session_id')
                  ->references('session_id')
                  ->on('teacher_assessment_sessions')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_results', function (Blueprint $table) {
            // Drop foreign key and session_id column
            $table->dropForeign(['session_id']);
            $table->dropColumn('session_id');
            
            // Restore answers column
            $table->json('answers')->nullable();
        });
    }
};
