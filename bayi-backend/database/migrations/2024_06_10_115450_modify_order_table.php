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


            //incoming bank transfer fields
            //transfer_account_name
            $table->string('transfer_account_name')->nullable()->after('payment_token');
            //transfer_bank_name
            $table->string('transfer_bank_name')->nullable()->after('transfer_account_name');
            //transfer_reference_no
            $table->string('transfer_reference_no')->nullable()->after('transfer_bank_name');
            //transfer_date
            $table->date('transfer_date')->nullable()->after('transfer_reference_no');
            //waiting transfer_approve
            $table->boolean('waiting_transfer_approve')->default(false)->after('transfer_date');

            //currency_rate
            $table->float('currency_rate')->default(1)->after('converted_subtotal');

            //converted_discount_amount
            $table->float('converted_discount_amount')->default(0)->after('converted_subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('transfer_account_name');
            $table->dropColumn('transfer_bank_name');
            $table->dropColumn('transfer_reference_no');
            $table->dropColumn('transfer_date');
            $table->dropColumn('waiting_transfer_approve');
            $table->dropColumn('currency_rate');
            $table->dropColumn('converted_discount_amount');
        });
    }
};
