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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('plan_id')->nullable();
            $table->uuid('customer_id')->nullable();
            $table->uuid('user_id')->nullable();
            //order no
            $table->string('order_no')->nullable();

            $table->decimal('amount', 10, 2)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->string('payment_method')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('ip_address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('plans');
            $table->foreign('customer_id')->references('id')->on('customer');
            $table->foreign('user_id')->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
