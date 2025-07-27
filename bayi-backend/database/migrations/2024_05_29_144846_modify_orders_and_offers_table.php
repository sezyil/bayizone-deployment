<?php

use App\Models\Customer\CustomerOfferLine;
use App\Models\Customer\CustomerOrderLine;
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
        //add customer_order_lines and customer_offer_lines item id
        Schema::table('customer_order_lines', function (Blueprint $table) {
            $table->string('item_id')->nullable();
        });

        Schema::table('customer_offer_lines', function (Blueprint $table) {
            $table->string('item_id')->nullable();
        });

        CustomerOrderLine::all()->each(function ($orderLine) {
            $orderLine->update(['item_id' => $orderLine->generateItemId()]);
        });

        CustomerOfferLine::all()->each(function ($offerLine) {
            $offerLine->update(['item_id' => $offerLine->generateItemId()]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_order_lines', function (Blueprint $table) {
            $table->dropColumn('item_id');
        });

        Schema::table('customer_offer_lines', function (Blueprint $table) {
            $table->dropColumn('item_id');
        });
    }
};
