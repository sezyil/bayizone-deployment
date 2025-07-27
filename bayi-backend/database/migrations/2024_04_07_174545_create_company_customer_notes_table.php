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
        Schema::create('company_customer_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('company_customer_id')->comment('şirket müşteri id si');
            $table->string('note', 500)->nullable()->comment('not');

            $table->timestamps();
            //fk
            $table->foreign('customer_id')->references('id')->on('customer');
            $table->foreign('company_customer_id')->references('id')->on('company_customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_customer_notes');
    }
};
