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
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('is_active')->default(true);
            $table->string('name');
            //attributes
            $table->json('attributes')->nullable();
            //is_featured
            $table->boolean('is_featured')->default(false);
            //is_trial
            $table->boolean('is_trial')->default(false);
            //trial_days
            $table->integer('trial_days')->default(0);
            //user_count
            $table->integer('user_count')->default(0);
            //catalog_management
            $table->boolean('catalog_management')->default(false)->comment('Ürün Yönetimi');
            //create_proforma_invoices
            $table->boolean('proforma_invoices')->default(false)->comment('Proforma Fatura');
            //talepten teklif oluştur
            $table->boolean('create_proposal')->default(false)->comment('Talepten Teklif Oluştur');
            //teklif yönetimi
            $table->boolean('proposal_management')->default(false)->comment('Teklif Yönetimi');
            //satış yönetimi
            $table->boolean('sales_management')->default(false)->comment('Satış Yönetimi');
            //whatsapp_integration
            $table->boolean('whatsapp_integration')->default(false)->comment('Whatsapp Entegrasyonu');
            //link ile teklif gönderme
            $table->boolean('send_proposal_with_link')->default(false)->comment('Link ile Teklif Gönderme');
            //basit muhasebe
            $table->boolean('simple_accounting')->default(false)->comment('Basit Muhasebe');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
