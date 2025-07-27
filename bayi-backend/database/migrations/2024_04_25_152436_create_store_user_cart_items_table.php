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
        Schema::create('store_user_cart_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_user_id');
            $table->uuid('product_uuid');
            $table->integer('quantity');
            $table->timestamps();

            //fk
            $table->foreign('store_user_id')->references('id')->on('store_users')->cascadeOnDelete();
            $table->foreign('product_uuid')->references('uuid')->on('products')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_user_cart_items');
    }
};
