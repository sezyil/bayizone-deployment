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
        //product prices
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price');
            //tl
            $table->decimal('price_tl', 15, 2)->default(0.00);
            //usd
            $table->decimal('price_usd', 15, 2)->default(0.00);
            //euro
            $table->decimal('price_euro', 15, 2)->default(0.00);
            //gbp
            $table->decimal('price_gbp', 15, 2)->default(0.00);
            //default currency
            $table->string('default_currency', 10)->default('tl');


            //fk
            $table->foreign('default_currency')->references('code')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['default_currency']);
            $table->dropColumn('price_tl');
            $table->dropColumn('price_usd');
            $table->dropColumn('price_euro');
            $table->dropColumn('price_gbp');
            $table->dropColumn('default_currency');

            $table->decimal('price', 15, 2)->default(0.00);
        });
    }
};
