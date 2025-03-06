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
        Schema::table('social_media_reports', function (Blueprint $table) {
            $table->foreignId('category_id')->after('id')->constrained('categories')->onDelete('cascade');
            $table->string('url')->after('category_id');
            $table->timestamp('posting_date')->after('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_media_reports', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'url', 'posting_date']);
        });
    }
};
