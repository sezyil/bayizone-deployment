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
        Schema::create('store_user_cart_item_variants', function (Blueprint $table) {
            $table->id();
            $table->uuid('store_user_cart_item_id');
            $table->string('type');
            $table->json('value');
            $table->timestamps();
            $table->foreign('store_user_cart_item_id')->references('id')->on('store_user_cart_items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_user_cart_item_variants');
    }
};
