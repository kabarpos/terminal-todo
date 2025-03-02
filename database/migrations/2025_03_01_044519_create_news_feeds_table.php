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
        Schema::create('news_feeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('news'); // news, video, social_media, image
            $table->string('url')->nullable(); // untuk link berita, video, social media
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable(); // untuk uploaded image
            $table->string('image_url')->nullable(); // untuk image dari metadata atau upload
            $table->string('video_url')->nullable();
            $table->string('site_name')->nullable();
            $table->json('meta_data')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_feeds');
    }
};
