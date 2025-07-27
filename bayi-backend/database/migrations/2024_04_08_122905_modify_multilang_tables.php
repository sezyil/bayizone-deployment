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
        //categories table customer_id nullable
        Schema::table('categories', function (Blueprint $table) {
            $table->uuid('customer_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //categories table customer_id not nullable
        Schema::table('categories', function (Blueprint $table) {
            $table->uuid('customer_id')->nullable(false)->change();
        });
    }
};
