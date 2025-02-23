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
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('team_id')->nullable()->constrained();
            $table->string('metric_type'); // task_completion, deadline_compliance, etc
            $table->decimal('value', 10, 2);
            $table->json('details')->nullable();
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->timestamps();
        });

        // Table for task time tracking
        Schema::create('task_time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_time_logs');
        Schema::dropIfExists('performance_metrics');
    }
};
