<?php

use App\Enums\Shipment\CustomerShipmentStatusEnum;
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
       Schema::dropIfExists('customer_order_line_deliveries');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //none
    }
};
