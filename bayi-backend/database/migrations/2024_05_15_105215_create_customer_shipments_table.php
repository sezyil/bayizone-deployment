<?php

use App\Enums\Shipment\CustomerShipmentStatusEnum;
use App\Models\System\Currency;
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
        Schema::create('customer_shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('company_customer_id')->nullable();
            $table->string('company_customer_name');
            $table->string('shipment_no')->unique();
            $table->string('container_no')->nullable();
            $table->string('carrier')->nullable();
            $table->string('status')->default(CustomerShipmentStatusEnum::DRAFT->value);
            $table->mediumText('note')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            //total_price
            $table->decimal('total_price', 15, 2)->default(0);
            //total_tax
            $table->decimal('total_tax', 15, 2)->default(0);
            //total_discount
            $table->decimal('total_discount', 15, 2)->default(0);
            //grand_total
            $table->decimal('grand_total', 15, 2)->default(0);
            //is_international
            $table->boolean('is_international')->default(false);
            $table->string('currency', Currency::CODE_LENGTH)->comment('para birimi');
            //total_volume
            $table->decimal('total_volume', 15, 2)->default(0);
            //total_package
            $table->decimal('total_package', 15, 2)->default(0);
            //total_weight
            $table->decimal('total_weight', 15, 2)->default(0);
            $table->timestamps();

            //fk
            $table->foreign('customer_id')->references('id')->on('customer')->cascadeOnDelete();
            $table->foreign('company_customer_id')->references('id')->on('company_customers')->nullOnDelete();
            $table->foreign('currency')->references('code')->on('currencies'); // para birimi ile ilişkilendirildi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_shipments');
    }
};
