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
        Schema::create('company_customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            //personal
            $table->string('authorized_name')->nullable()->comment('Yetkili adı');
            $table->string('authorized_surname')->nullable()->comment('Yetkili soyadı');
            //vergi daire
            $table->string('tax_office')->nullable();
            //company
            $table->string('tax_identity_no')->nullable(); //vergi kimlik no yada tc
            $table->string('company_name')->nullable(); //firma adı
            $table->string('phone'); //telefon
            $table->string('fax')->nullable();
            $table->string('email');
            //address
            $table->string('address');
            $table->unsignedMediumInteger('country_id');
            $table->unsignedMediumInteger('state_id')->nullable();
            $table->unsignedMediumInteger('city_id')->nullable();
            $table->string('postcode');
            //company

            // 1: tüzel kişi 2: gerçek kişi
            $table->integer('type')->default(1);

            //status
            $table->boolean('status')->default(true);


            $table->timestamps();
            //foreign keys
            $table->foreign('customer_id')->references('id')->on('customer')->cascadeOnDelete();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('state_id')->references('id')->on('states')->nullOnDelete();
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_customers');
    }
};
