<?php

use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer;
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
        //add to code column to customer table
        Schema::table('company_customers', function (Blueprint $table) {
            $table->string('code')->unique()->nullable();
        });

        //remove company_customer_users unique index
        Schema::table('company_customer_users', function (Blueprint $table) {
            $table->dropUnique('company_customer_users_email_unique');
        });

        //loop all customer
        $customers = CompanyCustomer::all();

        foreach ($customers as $customer) {
            if (empty($customer->code)) {
                $customer->code = $customer->generateCode();
                $customer->save();
            }
        }

        //add code password_reset_tokens table
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->string('code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_customers', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('company_customer_users', function (Blueprint $table) {
            $table->unique('email');
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
