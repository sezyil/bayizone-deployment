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
        //add delivered_quantity
        Schema::table('customer_order_lines', function (Blueprint $table) {
            $table->integer('delivered_quantity')->default(0)->after('remaining_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_order_lines', function (Blueprint $table) {
            $table->dropColumn('delivered_quantity');
        });
    }
};
