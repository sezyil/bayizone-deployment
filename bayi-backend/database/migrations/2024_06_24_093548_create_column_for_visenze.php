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
        Schema::table('customer', function (Blueprint $table) {
            $table->boolean('ai_support')->default(false);
            $table->integer('ai_catalog_id')->nullable();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('ai_sync')->default(false);
            $table->timestamp('ai_last_sync')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('ai_support');
            $table->dropColumn('ai_catalog_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('ai_sync');
            $table->dropColumn('ai_last_sync');
        });
    }
};
