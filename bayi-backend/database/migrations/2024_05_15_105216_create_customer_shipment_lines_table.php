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
        Schema::create('customer_shipment_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_shipment_id');
            $table->unsignedBigInteger('customer_order_line_id');
            $table->string('line_no')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_volume', 15, 2)->default(0);
            $table->decimal('unit_package', 15, 2)->default(0);
            $table->decimal('total_volume', 15, 2)->default(0);
            $table->decimal('total_package', 15, 2)->default(0);
            $table->decimal('weight', 15, 2)->default(0);
            $table->decimal('total_weight', 15, 2)->default(0);
            $table->string('note')->nullable();
            $table->timestamps();

            //fk
            $table->foreign('customer_shipment_id')->references('id')->on('customer_shipments')->cascadeOnDelete();
            $table->foreign('customer_order_line_id')->references('id')->on('customer_order_lines')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_shipment_lines');
    }
};
