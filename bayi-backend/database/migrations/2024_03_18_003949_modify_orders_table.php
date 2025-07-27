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
            //firm_name
            $table->string('invoice_firm_name')->nullable();
            //tax_no
            $table->string('invoice_tax_no')->nullable();
            //tax_administration
            $table->string('invoice_tax_administration')->nullable();
            //address
            $table->string('invoice_address')->nullable();
            //country_id
            $table->unsignedBigInteger('invoice_country_id')->nullable();
            //country
            $table->string('invoice_country')->nullable();
            //state_id
            $table->unsignedBigInteger('invoice_state_id')->nullable();
            //state
            $table->string('invoice_state')->nullable();
            //city_id
            $table->unsignedBigInteger('invoice_city_id')->nullable();
            //city
            $table->string('invoice_city')->nullable();
            //postcode
            $table->string('invoice_postcode')->nullable();
            //email
            $table->string('invoice_email')->nullable();
            //phone
            $table->string('invoice_phone')->nullable();
            //tax_rate
            $table->decimal('tax_rate', 5, 2)->default(0);
            //tax_amount
            $table->decimal('tax_amount', 15, 2)->default(0);
            //subtotal
            $table->decimal('subtotal', 15, 2)->default(0);
            //quantity
            $table->integer('quantity')->default(1);
            //amount -> total
            $table->renameColumn('amount', 'total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('total', 'amount');
            $table->dropColumn('invoice_firm_name');
            $table->dropColumn('invoice_tax_no');
            $table->dropColumn('invoice_tax_administration');
            $table->dropColumn('invoice_address');
            $table->dropColumn('invoice_country_id');
            $table->dropColumn('invoice_country');
            $table->dropColumn('invoice_state_id');
            $table->dropColumn('invoice_state');
            $table->dropColumn('invoice_city_id');
            $table->dropColumn('invoice_city');
            $table->dropColumn('invoice_postcode');
            $table->dropColumn('invoice_email');
            $table->dropColumn('invoice_phone');
            $table->dropColumn('tax_rate');
            $table->dropColumn('tax_amount');
            $table->dropColumn('subtotal');
            $table->dropColumn('quantity');
        });
    }
};
