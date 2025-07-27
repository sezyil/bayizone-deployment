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
        Schema::create('customer_offer_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_offer_id');

            //product info
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name', 50)->nullable()->comment('ürün adı');
            $table->string('product_code', 50)->nullable()->comment('ürün kodu');
            $table->string('product_unit', 50)->nullable()->comment('ürün birimi');
            $table->string('product_image_url')->nullable()->comment('ürün resim url si');


            $table->unsignedBigInteger('unit_id')->nullable()->comment('birim id');
            $table->decimal('quantity', 10, 2)->comment('miktar');

            $table->decimal('unit_price', 10, 2)->comment('birim fiyat');
            $table->decimal('tax_rate', 10, 2)->comment('birim kdv oranı');
            $table->decimal('unit_discount_price', 10, 2)->comment('birim iskonto fiyatı');
            $table->decimal('unit_discount_rate', 10, 2)->comment('birim iskonto oranı');


            $table->decimal('total_discount_price', 10, 2)->comment('toplam iskonto fiyatı');
            $table->decimal('total_price', 10, 2)->comment('toplam fiyat (kdv hariç)');
            $table->decimal('grand_total', 10, 2)->comment('toplam fiyat (kdv dahil)');


            $table->string('note', 500)->nullable()->comment('ürün notu');

            //foreign keys
            $table->foreign('customer_offer_id')->references('id')->on('customer_offers')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('unit_id')->references('id')->on('units');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_offer_lines');
    }
};
