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
            //app_key
            $table->string('app_key', 255)->nullable()->after('status');
            //secret_key
            $table->string('secret_key', 255)->nullable()->after('app_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer', function (Blueprint $table) {
            //app_key
            $table->dropColumn('app_key');
            //secret_key
            $table->dropColumn('secret_key');
        });
    }
};
