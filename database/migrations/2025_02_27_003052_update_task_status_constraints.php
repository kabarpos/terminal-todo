<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing tasks with in_review or approved status to draft
        DB::table('tasks')
            ->whereIn('status', ['in_review', 'approved'])
            ->update(['status' => 'draft']);

        // Drop the existing column
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Recreate the column with new enum values
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('status', ['draft', 'in_progress', 'completed', 'cancelled'])->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the existing column
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Recreate the column with old enum values
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('status', ['draft', 'in_review', 'approved', 'in_progress', 'completed', 'cancelled'])->default('draft');
        });
    }
};
