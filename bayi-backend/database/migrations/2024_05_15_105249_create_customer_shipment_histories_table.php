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
        Schema::create('customer_shipment_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('customer_shipment_id');
            $table->string('note')->nullable();
            $table->string('status')->nullable();
            $table->boolean('notify')->default(false);
            $table->timestamps();

            //fk
            $table->foreign('customer_shipment_id')->references('id')->on('customer_shipments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_shipment_histories');
    }
};
