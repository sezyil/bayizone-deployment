<?php

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
        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('currency', Currency::CODE_LENGTH)->nullable()->default(Currency::CODE_USD);
            //product group
            $table->string('product_group')->comment('all, subscription, addon');
            $table->boolean('customer_based')->default(false);
            $table->integer('limit')->nullable();
            $table->integer('use_count')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_redeemed')->default(false);
            $table->dateTime('redeemed_at')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('admin')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('admin')->nullOnDelete();
            $table->foreignUuid('deleted_by')->nullable()->constrained('admin')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['is_active', 'is_redeemed', 'expires_at']);
            //currency fk
            $table->foreign('currency')->references('code')->on('currencies')->nullOnDelete();
        });

        Schema::create('coupon_customer', function (Blueprint $table) {
            $table->uuid('coupon_id');
            $table->foreign('coupon_id')->references('id')->on('coupons')->cascadeOnDelete();
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customer')->cascadeOnDelete();
            $table->primary(['coupon_id', 'customer_id']);
        });

        //modify order
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('coupon_id')->nullable()->after('tax_amount');
            $table->foreign('coupon_id')->references('id')->on('coupons')->nullOnDelete();

            $table->decimal('discount_amount', 15, 2)->default(0)->after('coupon_id');
            $table->integer('discount_percentage')->default(0)->after('discount_amount');
            $table->string('discount_currency', Currency::CODE_LENGTH)->nullable()->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn('coupon_id');
            $table->dropColumn('discount_amount');
            $table->dropColumn('discount_currency');
            $table->dropColumn('discount_percentage');
        });
        Schema::dropIfExists('coupon_customer');
        Schema::dropIfExists('coupons');
    }
};
