<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->longText('firm_name'); //firma adı
            $table->string('tax_no')->unique(); //vergi no
            $table->string('tax_administration')->nullable(); //vergi dairesi
            $table->longText('address')->nullable(); //adres
            $table->unsignedMediumInteger('country_id')->nullable();
            $table->unsignedMediumInteger('state_id')->nullable();
            $table->unsignedMediumInteger('city_id')->nullable();
            $table->string('postcode')->nullable(); //posta kodu
            $table->string('email')->nullable(); //email
            $table->string('phone')->nullable(); //telefon
            $table->string('fax')->nullable(); //fax
            $table->boolean('status')->default(true); //durum
            $table->boolean('wizard_completed')->default(false); //sihirbaz tamamlandı
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
            $table->foreign('state_id')->references('id')->on('states')->nullOnDelete();
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer');
    }
};
