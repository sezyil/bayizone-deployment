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
        //customer_order_histories add column notify
        Schema::table('customer_order_histories', function (Blueprint $table) {
            $table->boolean('notify')->default(0)->after('note');
        });

        //customer_orders add column order_no
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->string('order_no')->nullable()->after('grand_total')->index();
            //is_international
            $table->boolean('is_international')->default(0)->after('billing_country_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_order_histories', function (Blueprint $table) {
            $table->dropColumn('notify');
        });

        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn('order_no');
            $table->dropColumn('is_international');
        });
    }
};
