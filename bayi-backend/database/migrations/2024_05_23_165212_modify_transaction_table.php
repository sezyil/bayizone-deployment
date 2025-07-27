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
        Schema::table('customer_transactions', function (Blueprint $table) {
            //drop
            $table->dropColumn('is_return');
            $table->dropColumn('is_cancelled');
            $table->dropColumn('total');
            $table->dropColumn('total_vat');
            $table->dropColumn('total_discount');
            $table->dropColumn('total_net');
            $table->dropColumn('total_paid');
            $table->dropColumn('total_due');
            $table->dropColumn('balance');
            //amount
            $table->float('amount', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            //drop
            $table->dropColumn('amount');
            //add
            $table->boolean('is_return')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->float('total', 15, 2)->default(0);
            $table->float('total_vat', 15, 2)->default(0);
            $table->float('total_discount', 15, 2)->default(0);
            $table->float('total_net', 15, 2)->default(0);
            $table->float('total_paid', 15, 2)->default(0);
            $table->float('total_due', 15, 2)->default(0);
            $table->float('balance', 15, 2)->default(0);
        });
    }
};
