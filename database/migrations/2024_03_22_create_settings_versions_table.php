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
        Schema::create('settings_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version_name');
            $table->text('description')->nullable();
            $table->json('settings_data');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_backup')->default(false);
            $table->timestamps();
            
            $table->index('created_at');
            $table->index('is_backup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_versions');
    }
}; 