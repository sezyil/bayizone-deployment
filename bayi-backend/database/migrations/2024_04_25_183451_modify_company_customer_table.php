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
        //authorized_name and authorized_surname merged into authorized_name
        Schema::table('company_customers', function (Blueprint $table) {
            $table->dropColumn('authorized_surname');
            $table->string('group', 20)->default('CUSTOMER')->after('authorized_name')->comment('Müşteri Grubu');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_customers', function (Blueprint $table) {
            $table->string('authorized_surname')->nullable();
            $table->dropColumn('group');
        });
    }
};
