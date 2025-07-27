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
        Schema::create('customer_order_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('customer_order_id');
            $table->string('status_code', 50)->comment('durum kodu');
            $table->string('note', 500)->nullable()->comment('not');
            $table->timestamps();

            //fk
            $table->foreign('customer_order_id')->references('id')->on('customer_orders')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_order_histories');
    }
};
