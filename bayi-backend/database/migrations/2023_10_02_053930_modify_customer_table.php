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
        //add balance column to company_customers table
        Schema::table('company_customers', function (Blueprint $table) {
            $table->decimal('balance', 10, 2)->default(0)->after('postcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop balance column from customer table
        Schema::table('company_customers', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
};
