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
        //add column customer_offers table closed_at and closed_by
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->dateTime('closed_date')->default(null)->nullable();
            $table->string('closed_user_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop column customer_offers table closed_at and closed_by
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->dropColumn('closed_date');
            $table->dropColumn('closed_user_name');
        });
    }
};
