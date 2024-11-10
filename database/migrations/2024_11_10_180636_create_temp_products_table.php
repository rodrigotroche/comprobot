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
        Schema::create('temp_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id');
            $table->string('reference_id');
            $table->string('name');
            $table->string('url');
            $table->string('image')->nullable();
            $table->decimal('current_price', 10, 2);
            $table->enum('status', ['pending', 'processed', 'error'])->default('pending');
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_products');
    }
};
