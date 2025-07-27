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
        Schema::table('products', function (Blueprint $table) {
            //store_visibility
            $table->boolean('store_visibility')->default(false)->after('status');
            //active_customer_group array
            $table->json('active_customer_group')->nullable()->after('store_visibility');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('store_visibility');
            $table->dropColumn('active_customer_group');
        });
    }
};
