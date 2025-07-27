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
        Schema::create('customer_bank_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->string('bank_name'); // banka adı
            $table->string('branch_name'); // şube adı
            $table->string('account_name'); // hesap adı
            $table->string('account_number')->nullable(); // hesap numarası
            $table->string('iban')->nullable(); // iban
            $table->string('swift_code')->nullable(); // swift kodu
            //currency from settings
            $table->string('currency', 10); // para birimi
            $table->boolean('status')->default(true);

            //foreign keys
            $table->foreign('customer_id')->references('id')->on('customer')->cascadeOnDelete(); // müşteri id si ile ilişkilendirildi
            $table->foreign('currency')->references('code')->on('currencies'); // para birimi id si ile ilişkilendirildi

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_bank_accounts');
    }
};
