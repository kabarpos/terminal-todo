<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'rejected', 'banned', 'inactive'])
                ->default('pending')
                ->after('remember_token');
            $table->text('status_reason')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('status_reason');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status', 'status_reason', 'approved_at', 'approved_by']);
        });
    }
}; 