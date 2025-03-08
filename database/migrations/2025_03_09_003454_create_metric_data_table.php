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
        Schema::create('metric_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('social_accounts')->onDelete('cascade');
            $table->foreignId('metric_id')->constrained('metrics')->onDelete('cascade');
            $table->decimal('value', 20, 2);
            $table->unsignedTinyInteger('week_number');
            $table->unsignedTinyInteger('month');
            $table->year('year');
            $table->timestamp('recorded_at');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Composite index untuk performa query
            $table->index(['account_id', 'metric_id', 'year', 'month', 'week_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metric_data');
    }
};
