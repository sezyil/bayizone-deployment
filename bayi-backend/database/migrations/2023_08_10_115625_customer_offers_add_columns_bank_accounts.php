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
        /* add columns to customer_offers */
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->string('payment_bank_name', 191)->after('billing_zip_code')->comment('ödeme bankası');
            $table->string('payment_branch_name', 191)->after('payment_bank_name')->comment('ödeme banka şubesi');
            $table->string('payment_account_name', 191)->after('payment_branch_name')->comment('ödeme hesap adı');
            $table->string('payment_account_number', 191)->after('payment_account_name')->comment('ödeme hesap numarası');
            $table->string('payment_iban', 191)->after('payment_account_number')->comment('ödeme iban numarası');
            $table->string('payment_swift_code', 191)->after('payment_iban')->nullable()->comment('ödeme swift kodu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /* drop columns from customer_offers */
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->dropColumn('payment_bank_name');
            $table->dropColumn('payment_branch_name');
            $table->dropColumn('payment_account_name');
            $table->dropColumn('payment_account_number');
            $table->dropColumn('payment_iban');
            $table->dropColumn('payment_swift_code');
        });
    }
};
