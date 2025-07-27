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
        Schema::create('customer_order_line_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_order_line_id')->constrained()->onDelete('cascade');
            $table->integer('sent_quantity')->default(0);
            $table->integer('remaining_quantity')->default(0);
            $table->timestamp('operation_date')->nullable();
            //note
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::table('customer_order_lines', function (Blueprint $table) {
            $table->integer('sent_quantity')->default(0);
            $table->string('custom_order_number')->nullable();
            $table->integer('remaining_quantity')->default(0);
            $table->boolean('completed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_order_line_histories');

        Schema::table('customer_order_lines', function (Blueprint $table) {
            $table->dropColumn('sent_quantity');
            $table->dropColumn('remaining_quantity');
            $table->dropColumn('completed');
            $table->dropColumn('custom_order_number');
        });
    }
};
