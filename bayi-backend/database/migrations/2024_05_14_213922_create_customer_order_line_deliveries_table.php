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
        Schema::create('customer_order_line_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_order_line_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->datetime('shipping_at')->nullable();
            $table->boolean('shipped')->default(false);
            $table->string('delivery_code')->nullable();
            $table->string('delivery_note')->nullable();
            $table->boolean('delivered')->default(false);
            $table->timestamp('delivered_at')->nullable();
            $table->boolean('notify')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_order_line_deliveries');
    }
};
