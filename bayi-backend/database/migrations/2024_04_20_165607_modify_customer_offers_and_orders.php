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
        //add incoterms to customer_offers and customer_orders
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->string('incoterms')->nullable();
        });

        Schema::table('customer_orders', function (Blueprint $table) {
            $table->string('incoterms')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->dropColumn('incoterms');
        });

        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn('incoterms');
        });
    }
};
