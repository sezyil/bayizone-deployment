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
        Schema::create('customer_transaction_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_transaction_id');
            $table->string('description')->nullable();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('vat_rate', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('total_vat', 10, 2)->default(0);

            $table->timestamps();

            //foreign keys
            $table->foreign('customer_transaction_id')->references('id')->on('customer_transactions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_transaction_lines');
    }
};
