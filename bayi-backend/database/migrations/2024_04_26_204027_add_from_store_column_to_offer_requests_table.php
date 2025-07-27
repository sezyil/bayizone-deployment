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
        Schema::table('offer_requests', function (Blueprint $table) {
            //from store bool
            $table->boolean('from_store')->default(false)->comment('is created from store');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_requests', function (Blueprint $table) {
            $table->dropColumn('from_store');
        });
    }
};
