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
        Schema::create('offer_request_line_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_request_line_id');
            $table->string('type');
            $table->json('value');
            $table->timestamps();
            $table->foreign('offer_request_line_id')->references('id')->on('offer_request_lines')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_request_line_variants');
    }
};
