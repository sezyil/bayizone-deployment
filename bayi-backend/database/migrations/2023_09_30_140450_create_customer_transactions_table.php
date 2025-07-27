<?php

use App\Enums\CustomerTransactionsFicheTypeEnum;
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
        Schema::create('customer_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('company_customer_id')->nullable();
            $table->uuid('bank_account_id');
            $table->string('fiche_no')->nullable(); //fiş no
            $table->enum('fiche_type', CustomerTransactionsFicheTypeEnum::all());
            $table->date('date'); //tarih
            //description bigger than 255
            $table->text('description')->nullable(); //açıklama
            $table->boolean('is_paid')->default(false)->comment('Ödendi mi?'); //ödendi mi
            $table->boolean('is_return')->default(false); //iade mi
            $table->boolean('is_cancelled')->default(false); //iptal mi
            $table->boolean('io_type')->default(false)->comment('giriş mi çıkış mı (giriş 0, çıkış 1)'); //giriş mi çıkış mı (giriş 0, çıkış 1)
            $table->decimal('total', 10, 2)->default(0); //toplam
            $table->decimal('total_vat', 10, 2)->default(0); //toplam kdv
            $table->decimal('total_discount', 10, 2)->default(0); //toplam indirim
            $table->decimal('total_net', 10, 2)->default(0); //toplam net
            $table->decimal('total_paid', 10, 2)->default(0); //toplam ödenen
            $table->decimal('total_due', 10, 2)->default(0); //toplam kalan

            //due date
            $table->date('due_date')->nullable(); //vade tarihi



            $table->timestamps();

            //foreign keys
            $table->foreign('customer_id')->references('id')->on('customer')->cascadeOnDelete();
            $table->foreign('company_customer_id')->references('id')->on('company_customers')->cascadeOnDelete();
            $table->foreign('bank_account_id')->references('id')->on('customer_bank_accounts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_transactions');
    }
};
