<?php

use App\Models\System\Currency;
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
        //add customer_transactions table column customer_order_id
        Schema::table('customer_transactions', function (Blueprint $table) {
            //drop bank_account_id
            $table->dropForeign(['bank_account_id']);
            $table->dropColumn('bank_account_id');

            $table->uuid('customer_order_id')->nullable();
            $table->foreign('customer_order_id')->references('id')->on('customer_orders')->nullOnDelete();
            //balance
            $table->decimal('balance', 15, 2)->default(0);
            //currency
            $table->string('currency', Currency::CODE_LENGTH)->default(Currency::DEFAULT_CURRENCY);

            //foreign keys
            $table->foreign('currency')->references('code')->on('currencies')->cascadeOnDelete();
        });
        //drop customer_transaction_lines table
        Schema::dropIfExists('customer_transaction_lines');
        Schema::dropIfExists('customer_transaction_histories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_transaction_histories');
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->uuid('bank_account_id')->nullable();
            $table->foreign('bank_account_id')->references('id')->on('customer_bank_accounts')->nullOnDelete();
            $table->dropForeign(['customer_order_id']);
            $table->dropForeign(['currency']);
            $table->dropColumn('customer_order_id');
            $table->dropColumn('balance');
            $table->dropColumn('currency');
        });
    }
};
