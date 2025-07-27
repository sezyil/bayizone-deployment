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
        Schema::create('company_customer_balances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('currency', Currency::CODE_LENGTH)->default(Currency::DEFAULT_CURRENCY)->comment('Para birimi');
            $table->uuid('company_customer_id');
            $table->float('balance', 15, 2)->default(0)->comment('Bakiye');
            $table->float('total_debt', 15, 2)->default(0)->comment('Toplam borç');
            $table->float('total_credit', 15, 2)->default(0)->comment('Toplam alacak');
            $table->float('total_paid_debt', 15, 2)->default(0)->comment('Toplam ödenmiş borç');
            $table->float('total_paid_credit', 15, 2)->default(0)->comment('Toplam ödenmiş alacak');
            $table->float('total_unpaid_debt', 15, 2)->default(0)->comment('Toplam ödenmemiş borç');
            $table->float('total_unpaid_credit', 15, 2)->default(0)->comment('Toplam ödenmemiş alacak');
            $table->float('total_overdue_debt', 15, 2)->default(0)->comment('Toplam vadesi geçmiş borç');
            $table->float('total_overdue_credit', 15, 2)->default(0)->comment('Toplam vadesi geçmiş alacak');
            $table->timestamps();
            $table->foreign('company_customer_id')->references('id')->on('company_customers')->cascadeOnDelete();
            $table->foreign('currency')->references('code')->on('currencies');
        });


        $this->createCompanyCustomerBalances();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_customer_balances');
    }

    private function createCompanyCustomerBalances(): void
    {
        $companyCustomerBalances = \App\Models\CompanyCustomer\CompanyCustomer::all();
        foreach ($companyCustomerBalances as $companyCustomerBalance) {
            $companyCustomerBalance->createBalance();
        }
    }
};
