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
        //drop
        Schema::table('customer_offers', function (Blueprint $table) {
            //drop fk
            $table->dropForeign('customer_offers_shipping_city_id_foreign');
            $table->dropForeign('customer_offers_shipping_state_id_foreign');
            $table->dropForeign('customer_offers_shipping_country_id_foreign');


            //shipping_address
            $table->dropColumn('shipping_address');
            //shipping_city_id delete
            $table->dropColumn('shipping_city_id');
            //shipping_state_id
            $table->dropColumn('shipping_state_id');
            //shipping_country_id
            $table->dropColumn('shipping_country_id');
            //shipping_zip_code
            $table->dropColumn('shipping_zip_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->string('shipping_address')->nullable()->comment('teslimat adresi');
            $table->unsignedMediumInteger('shipping_city_id')->nullable()->comment('teslimat şehri');
            $table->unsignedMediumInteger('shipping_state_id')->nullable()->comment('teslimat ilçesi');
            $table->unsignedMediumInteger('shipping_country_id')->nullable()->comment('teslimat ülkesi');
            $table->string('shipping_zip_code')->comment('teslimat posta kodu');

            //fk
            $table->foreign('shipping_city_id')->references('id')->on('cities'); // teslimat şehri ile ilişkilendirildi
            $table->foreign('shipping_state_id')->references('id')->on('states'); // teslimat ilçesi ile ilişkilendirildi
            $table->foreign('shipping_country_id')->references('id')->on('countries'); // teslimat ülkesi ile ilişkilendirildi
        });
    }
};
