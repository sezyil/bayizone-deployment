<?php

use App\Enums\CustomerOrderLineStatusEnum;
use App\Models\Customer\CustomerOrderLineHistory;
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
        Schema::table('customer_order_line_histories', function (Blueprint $table) {
            $table->string('status')->default(CustomerOrderLineStatusEnum::PENDING->value);
            $table->boolean('notify')->default(false);

            //sent_quantity column is removed
            $table->dropColumn('sent_quantity');
            //remaining_quantity column is removed
            $table->dropColumn('remaining_quantity');
            //operation_date column is removed
            $table->dropColumn('operation_date');
        });

        Schema::table('customer_order_lines', function (Blueprint $table) {
            $table->string('status')->default(CustomerOrderLineStatusEnum::PENDING->value);
            //drop custom_order_number column
            $table->dropColumn('custom_order_number');
            //completed column is removed
            $table->dropColumn('completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_order_line_histories', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('notify');

            $table->integer('sent_quantity')->default(0);
            $table->integer('remaining_quantity')->default(0);
            $table->timestamp('operation_date')->nullable();
        });

        Schema::table('customer_order_lines', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->string('custom_order_number')->nullable();
            $table->boolean('completed')->default(false);
        });
    }
};
