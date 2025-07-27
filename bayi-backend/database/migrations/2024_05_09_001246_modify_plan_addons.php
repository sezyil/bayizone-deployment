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
        //plan_addons bulk_quantity
        Schema::table('plan_addons', function (Blueprint $table) {
            $table->boolean('is_bulk')->default(0)->after('price');
            $table->integer('bulk_quantity')->default(0)->after('is_bulk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_addons', function (Blueprint $table) {
            $table->dropColumn('is_bulk');
            $table->dropColumn('bulk_quantity');
        });
    }
};
