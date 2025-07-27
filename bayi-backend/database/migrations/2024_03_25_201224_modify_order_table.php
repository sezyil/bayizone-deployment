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
        Schema::table('orders', function (Blueprint $table) {
            //drop plan_id
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');

            //drop tax_rate
            $table->dropColumn('tax_rate');
            //drop quantity
            $table->dropColumn('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('plan_id')->nullable();
            $table->foreign('plan_id')->references('id')->on('plans');
            //tax_rate
            $table->decimal('tax_rate', 5, 2)->default(0);
            //quantity
            $table->integer('quantity')->default(1);
        });
    }
};
