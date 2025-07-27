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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('customer_id')->nullable();
            $table->uuid('plan_id')->nullable();
            $table->uuid('order_id')->nullable();
            $table->boolean('is_trial')->default(false);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            //is_active
            $table->boolean('is_active')->default(true);
            //user_count
            $table->integer('user_count')->default(0);
            //left_user_count
            $table->integer('left_user_count')->default(0);
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

            $table->foreign('customer_id')->references('id')->on('customer');
            $table->foreign('plan_id')->references('id')->on('plans');
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
