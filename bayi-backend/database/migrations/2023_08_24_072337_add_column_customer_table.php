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
        //add column to customer table,image
        Schema::table('customer', function (Blueprint $table) {
            $table->string('image')->nullable()->after('firm_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop column from customer table,image
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
