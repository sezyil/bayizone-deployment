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
        /* customer offers total volume and package */
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->decimal('total_volume', 15, 2)->default(0);
            $table->decimal('total_package', 15, 2)->default(0);
        });

        Schema::table('customer_offer_lines', function (Blueprint $table) {
            $table->decimal('unit_volume', 15, 2)->default(0);
            $table->decimal('total_volume', 15, 2)->default(0);
            $table->decimal('unit_package', 15, 2)->default(0);
            $table->decimal('total_package', 15, 2)->default(0);
        });

        //customer_orders
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->decimal('total_volume', 15, 2)->default(0);
            $table->decimal('total_package', 15, 2)->default(0);
        });

        //customer_order_lines
        Schema::table('customer_order_lines', function (Blueprint $table) {
            $table->decimal('unit_volume', 15, 2)->default(0);
            $table->decimal('total_volume', 15, 2)->default(0);
            $table->decimal('unit_package', 15, 2)->default(0);
            $table->decimal('total_package', 15, 2)->default(0);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->dropColumn('total_volume');
            $table->dropColumn('total_package');
        });

        Schema::table('customer_offer_lines', function (Blueprint $table) {
            $table->dropColumn('volume');
            $table->dropColumn('package');
        });

        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn('total_volume');
            $table->dropColumn('total_package');
        });

        Schema::table('customer_order_lines', function (Blueprint $table) {
            $table->dropColumn('volume');
            $table->dropColumn('package');
        });
    }
};
