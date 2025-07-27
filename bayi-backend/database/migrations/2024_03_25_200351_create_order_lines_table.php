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
        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('type')->comment('product, service, etc.');
            $table->string('name')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            //item_id dynamic
            $table->uuid('item_id')->nullable()->comment('product_id, service_id, etc.');
            //item_data dynamic
            $table->json('item_data')->nullable();
            //tax
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            //quantity
            $table->decimal('quantity', 10, 2)->default(0);
            //subtotal
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_lines');
    }
};
