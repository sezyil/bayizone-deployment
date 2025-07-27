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
        Schema::table('customer_offers', function (Blueprint $table) {
            //add to order table
            $table->uuid('customer_order_id')->nullable()->after('id')->comment('müşteri sipariş id si');

            $table->foreign('customer_order_id')->references('id')->on('customer_orders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->dropForeign(['customer_order_id']);
            $table->dropColumn('customer_order_id');
        });
    }
};
