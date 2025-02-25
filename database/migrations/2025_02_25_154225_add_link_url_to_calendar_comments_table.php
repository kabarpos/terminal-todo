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
        Schema::table('calendar_comments', function (Blueprint $table) {
            $table->string('link_url')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_type')->nullable();
            $table->string('attachment_name')->nullable();
            $table->integer('attachment_size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_comments', function (Blueprint $table) {
            $table->dropColumn('link_url');
            $table->dropColumn('attachment_path');
            $table->dropColumn('attachment_type');
            $table->dropColumn('attachment_name');
            $table->dropColumn('attachment_size');
        });
    }
};
