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
        Schema::create('customer_offer_line_variants', function (Blueprint $table) {
            $table->id();
            $table->uuid('customer_offer_line_id');
            $table->string('type');
            $table->json('value');
            $table->timestamps();
            $table->foreign('customer_offer_line_id')->references('id')->on('customer_offer_lines')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_offer_line_variants');
    }
};
