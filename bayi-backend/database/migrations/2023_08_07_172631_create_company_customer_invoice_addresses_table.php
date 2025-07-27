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
        Schema::create('company_customer_invoice_addresses', function (Blueprint $table) {
            $table->id();
            //customer_id uuid
            $table->uuid('customer_id'); //
            //company customer id
            $table->string('company_customer_id');
            $table->string('name'); // fatura adı
            $table->string('address'); // fatura adresi
            $table->string('phone'); // fatura telefonu
            $table->string('email'); // fatura email

            //address details
            $table->unsignedMediumInteger('country_id'); // ülke id
            $table->unsignedMediumInteger('city_id')->nullable(); // şehir id
            $table->unsignedMediumInteger('state_id')->nullable(); // ilçe id
            $table->string('zip_code')->nullable(); // posta kodu

            $table->timestamps();

            //fk
            $table->foreign('customer_id')->references('id')->on('customer')->onDelete('cascade');
            $table->foreign('company_customer_id')->references('id')->on('company_customers')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
            $table->foreign('state_id')->references('id')->on('states')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_customer_invoice_addresses');
    }
};
