<?php

use App\Models\Customer\CustomerOrderLineVariant;
use App\Models\OfferRequestLineVariant;
use App\Models\StoreUserCartItemVariant;
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
        //customer_order_line_variants
        CustomerOrderLineVariant::truncate();
        Schema::table('customer_order_line_variants', function (Blueprint $table) {
            //drop value
            $table->dropColumn('value');
            //product_variant_id
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            //product_variant_value_id
            $table->foreignUuid('product_variant_value_id')->nullable()->constrained('product_variant_values')->onDelete('cascade');
        });

        //customer_offer_line_variants
        CustomerOrderLineVariant::truncate();
        Schema::table('customer_offer_line_variants', function (Blueprint $table) {
            //drop value
            $table->dropColumn('value');
            //product_variant_id
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            //product_variant_value_id
            $table->foreignUuid('product_variant_value_id')->nullable()->constrained('product_variant_values')->onDelete('cascade');
        });

        //offer_request_line_variants
        OfferRequestLineVariant::truncate();
        Schema::table('offer_request_line_variants', function (Blueprint $table) {
            //drop value
            $table->dropColumn('value');
            //product_variant_id
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            //product_variant_value_id
            $table->foreignUuid('product_variant_value_id')->nullable()->constrained('product_variant_values')->onDelete('cascade');
        });

        //store_user_cart_item_variants
        StoreUserCartItemVariant::truncate();
        Schema::table('store_user_cart_item_variants', function (Blueprint $table) {
            //drop value
            $table->dropColumn('value');
            //product_variant_id
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            //product_variant_value_id
            $table->foreignUuid('product_variant_value_id')->nullable()->constrained('product_variant_values')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //customer_order_line_variants
        Schema::table('customer_order_line_variants', function (Blueprint $table) {
            //drop product_variant_id
            $table->dropConstrainedForeignId('product_variant_id');
            //drop product_variant_value_id
            $table->dropConstrainedForeignId('product_variant_value_id');
            //add value
            $table->json('value')->nullable();
        });

        //customer_offer_line_variants
        Schema::table('customer_offer_line_variants', function (Blueprint $table) {
            //drop product_variant_id
            $table->dropConstrainedForeignId('product_variant_id');
            //drop product_variant_value_id
            $table->dropConstrainedForeignId('product_variant_value_id');
            //add value
            $table->json('value')->nullable();
        });

        //offer_request_line_variants
        Schema::table('offer_request_line_variants', function (Blueprint $table) {
            //drop product_variant_id
            $table->dropConstrainedForeignId('product_variant_id');
            //drop product_variant_value_id
            $table->dropConstrainedForeignId('product_variant_value_id');
            //add value
            $table->json('value')->nullable();
        });

        //store_user_cart_item_variants
        Schema::table('store_user_cart_item_variants', function (Blueprint $table) {
            //drop product_variant_id
            $table->dropConstrainedForeignId('product_variant_id');
            //drop product_variant_value_id
            $table->dropConstrainedForeignId('product_variant_value_id');
            //add value
            $table->json('value')->nullable();
        });
    }
};
