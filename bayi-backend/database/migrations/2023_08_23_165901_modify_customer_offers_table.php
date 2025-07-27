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
        //add column to customer_offers table approve_date,reject_date,approved_user name,rejected_user name
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->dateTime('approve_date')->nullable()->after('status');
            $table->string('approved_user_name')->nullable()->after('approve_date');
            $table->dateTime('reject_date')->nullable()->after('approved_user_name');
            $table->string('rejected_user_name')->nullable()->after('reject_date');
            //cancelled_user_name
            $table->string('cancelled_user_name')->nullable()->after('rejected_user_name');
            //cancelled_date
            $table->dateTime('cancelled_date')->nullable()->after('cancelled_user_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop column from customer_offers table approve_date
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->dropColumn('approve_date');
            $table->dropColumn('approved_user_name');
            $table->dropColumn('reject_date');
            $table->dropColumn('rejected_user_name');
            $table->dropColumn('cancelled_user_name');
            $table->dropColumn('cancelled_date');
        });
    }
};
