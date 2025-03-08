<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metric_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('social_accounts')->onDelete('cascade');
            $table->foreignId('metric_id')->constrained('metrics')->onDelete('cascade');
            $table->decimal('target_value', 20, 2);
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->text('notes')->nullable();
            $table->boolean('is_achieved')->default(false);
            $table->timestamp('achieved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Composite index untuk performa query
            $table->index(['account_id', 'metric_id', 'period_start', 'period_end'], 'mt_account_metric_period_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_targets');
    }
}; 