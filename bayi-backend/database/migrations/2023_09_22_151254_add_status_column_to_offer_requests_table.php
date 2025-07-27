<?php

use App\Enums\OfferRequestStatusEnum;
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
            $table->enum('status', OfferRequestStatusEnum::getValues())->default(OfferRequestStatusEnum::PENDING->value)->after('global_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
