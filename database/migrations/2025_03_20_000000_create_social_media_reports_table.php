<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('social_media_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained('platforms')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('posting_date');
            $table->integer('reach')->default(0);
            $table->integer('impressions')->default(0);
            $table->integer('engagement')->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0);
            $table->integer('likes')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);
            $table->json('additional_metrics')->nullable();
            $table->text('insights')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('social_media_reports');
    }
}; 