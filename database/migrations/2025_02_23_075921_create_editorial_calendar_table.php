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
        Schema::create('editorial_calendar', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('platform_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('publish_date');
            $table->timestamp('deadline')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'published', 'cancelled'])->default('planned');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot table for calendar assignees
        Schema::create('editorial_calendar_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_id')->constrained('editorial_calendar')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->nullable(); // writer, editor, reviewer, etc
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editorial_calendar_assignees');
        Schema::dropIfExists('editorial_calendar');
    }
};
