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
        //add column is_main_user
        Schema::table('company_customer_users', function (Blueprint $table) {
            $table->boolean('is_main_user')->after('status')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_customer_users', function (Blueprint $table) {
            $table->dropColumn('is_main_user');
        });
    }
};
