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
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('company_customer_id')->comment('şirket müşteri id si');
            $table->string('currency', 10)->comment('para birimi');

            //currency
            $table->decimal('total_price', 10, 2)->comment('toplam fiyat (kdv ve indirim hariç)');
            //main price
            $table->decimal('total_tax', 10, 2)->comment('toplam kdv');
            $table->decimal('total_discount', 10, 2)->comment('toplam indirim');
            $table->decimal('grand_total', 10, 2)->comment('toplam fiyat (kdv ve indirim dahil)');
            $table->string('billing_address')->nullable()->comment('fatura adresi');
            $table->unsignedMediumInteger('billing_country_id')->comment('fatura ülkesi');
            $table->unsignedMediumInteger('billing_city_id')->nullable()->comment('fatura şehri');
            $table->unsignedMediumInteger('billing_state_id')->nullable()->comment('fatura ilçesi');
            $table->string('note', 500)->nullable()->comment('sipariş notu');

            $table->timestamps();

            //fk
            $table->foreign('customer_id')->references('id')->on('customer');
            $table->foreign('company_customer_id')->references('id')->on('company_customers');
            $table->foreign('billing_country_id')->references('id')->on('countries'); // fatura ülkesi ile ilişkilendirildi
            $table->foreign('billing_city_id')->references('id')->on('cities')->nullOnDelete(); // fatura şehri ile ilişkilendirildi
            $table->foreign('billing_state_id')->references('id')->on('states')->nullOnDelete(); // fatura ilçesi ile ilişkilendirildi
            $table->foreign('currency')->references('code')->on('currencies'); // para birimi ile ilişkilendirildi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_orders');
    }
};
