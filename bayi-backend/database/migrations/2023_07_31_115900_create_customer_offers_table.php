<?php

use App\Enums\CustomerOfferStatusEnum;
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
        Schema::create('customer_offers', function (Blueprint $table) {
            //uuid
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('company_customer_id')->comment('şirket müşteri id si');

            //currency
            $table->decimal('total_price', 10, 2)->comment('toplam fiyat (kdv ve indirim hariç)');
            //main price
            $table->decimal('total_tax', 10, 2)->comment('toplam kdv');
            $table->decimal('total_discount', 10, 2)->comment('toplam indirim');
            $table->decimal('grand_total', 10, 2)->comment('toplam fiyat (kdv ve indirim dahil)');


            //date
            $table->date('offer_date')->nullable()->comment('teklif tarihi');
            //offer_due_date
            $table->date('offer_due_date')->nullable()->comment('teklif geçerlilik tarihi');
            //string
            $table->string('offer_no', 50)->comment('teklif no (otomatik oluşturulacak)');
            $table->string('currency', 10)->comment('para birimi');
            $table->string('note', 500)->nullable()->comment('sipariş notu');

            //shipping address
            $table->string('shipping_address')->nullable()->comment('teslimat adresi');
            $table->unsignedMediumInteger('shipping_country_id')->comment('teslimat ülkesi');
            $table->unsignedMediumInteger('shipping_city_id')->nullable()->comment('teslimat şehri');
            $table->unsignedMediumInteger('shipping_state_id')->nullable()->comment('teslimat ilçesi');
            $table->string('shipping_zip_code')->comment('teslimat posta kodu');

            //billing address
            $table->string('billing_address')->nullable()->comment('fatura adresi');
            $table->unsignedMediumInteger('billing_country_id')->comment('fatura ülkesi');
            $table->unsignedMediumInteger('billing_city_id')->nullable()->comment('fatura şehri');
            $table->unsignedMediumInteger('billing_state_id')->nullable()->comment('fatura ilçesi');

            $table->string('billing_zip_code')->nullable()->comment('fatura posta kodu');

            //contact
            $table->string('contact_person', 50)->nullable()->comment('yetkili kişi');
            $table->string('contact_email', 50)->nullable()->comment('mail adresi');
            $table->string('contact_phone', 50)->nullable()->comment('telefon numarası');

            //termin tarihi

            //whatsapp notification date
            $table->date('whatsapp_notification_date')->nullable()->comment('whatsapp gönderim tarihi');
            //mail notification date
            $table->date('mail_notification_date')->nullable()->comment('mail gönderim tarihi');

            //password for offer
            $table->string('password')->nullable()->comment('teklif şifresi-müşteriye bildirim gönderildiğinde kullanılacak');

            //is request or create
            $table->boolean('is_request')->default(false)->comment('teklif isteği mi? (true ise teklif isteği, false ise teklif oluşturma)');
            $table->boolean('international_order')->default(false)->comment('yurtdışı sipariş mi?');

            //visible columns json
            $table->json('visible_columns')->nullable()->comment('görünen kolonlar');
            $table->enum('status', CustomerOfferStatusEnum::all())->default(CustomerOfferStatusEnum::DRAFT->value)->comment('teklif durumu');

            //foreign keys
            $table->foreign('customer_id')->references('id')->on('customer')->cascadeOnDelete(); // müşteri id si ile ilişkilendirildi
            $table->foreign('company_customer_id')->references('id')->on('company_customers')->cascadeOnDelete(); // şirket müşteri id si ile ilişkilendirildi
            $table->foreign('currency')->references('code')->on('currencies'); // para birimi ile ilişkilendirildi
            $table->foreign('billing_country_id')->references('id')->on('countries'); // fatura ülkesi ile ilişkilendirildi
            $table->foreign('billing_city_id')->references('id')->on('cities')->nullOnDelete(); // fatura şehri ile ilişkilendirildi
            $table->foreign('billing_state_id')->references('id')->on('states')->nullOnDelete(); // fatura ilçesi ile ilişkilendirildi
            $table->foreign('shipping_country_id')->references('id')->on('countries'); // teslimat ülkesi ile ilişkilendirildi
            $table->foreign('shipping_city_id')->references('id')->on('cities')->nullOnDelete(); // teslimat şehri ile ilişkilendirildi
            $table->foreign('shipping_state_id')->references('id')->on('states')->nullOnDelete(); // teslimat ilçesi ile ilişkilendirildi

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_offers');
    }
};
