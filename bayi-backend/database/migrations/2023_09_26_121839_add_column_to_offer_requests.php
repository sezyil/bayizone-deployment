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
        Schema::table('offer_requests', function (Blueprint $table) {
            //add offer_id string
            $table->string('customer_offer_id')->nullable()->after('id');

            //fk
            $table->foreign('customer_offer_id')->references('id')->on('customer_offers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_requests', function (Blueprint $table) {
            $table->dropForeign('offer_requests_customer_offer_id_foreign');
            $table->dropColumn('customer_offer_id');
        });
    }
};
