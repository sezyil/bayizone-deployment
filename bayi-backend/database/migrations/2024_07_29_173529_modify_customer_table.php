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
        //customer
        Schema::table('customer', function (Blueprint $table) {
            //drop unique from tax_no
            $table->dropUnique('customer_tax_no_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //customer
        Schema::table('customer', function (Blueprint $table) {
            //add unique to tax_no
            $table->unique('tax_no');
        });
    }
};
