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
        Schema::table('orders', function (Blueprint $table) {
            //converted_tax_amount
            $table->decimal('converted_tax_amount', 10, 2)->default(0);
            //converted_total
            $table->decimal('converted_total', 10, 2)->default(0);
            //converted_sub_total
            $table->decimal('converted_subtotal', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('converted_tax_amount');
            $table->dropColumn('converted_total');
            $table->dropColumn('converted_subtotal');
        });
    }
};
