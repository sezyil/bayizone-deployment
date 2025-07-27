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
        Schema::table('customer_orders', function (Blueprint $table) {
            //add delivery_type,payment_type
            $table->string('delivery_type')->nullable();
            $table->string('payment_type')->nullable();
        });
        Schema::table('customer_offers', function (Blueprint $table) {
            //add delivery_type,payment_type
            $table->string('delivery_type')->nullable();
            $table->string('payment_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn('delivery_type');
            $table->dropColumn('payment_type');
        });

        Schema::table('customer_offers', function (Blueprint $table) {
            $table->dropColumn('delivery_type');
            $table->dropColumn('payment_type');
        });
    }
};
