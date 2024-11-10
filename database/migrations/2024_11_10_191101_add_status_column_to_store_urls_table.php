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
        Schema::table('store_urls', function (Blueprint $table) {
            $table->enum('status', ['pending', 'in_progress', 'processed', 'error'])->default('pending')->after('enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_urls', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
