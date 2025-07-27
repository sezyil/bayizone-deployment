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
        Schema::create('customer_order_line_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_order_line_id');
            $table->string('type');
            $table->json('value');
            $table->timestamps();
            $table->foreign('customer_order_line_id')->references('id')->on('customer_order_lines')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_order_line_variants');
    }
};
