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
        //add user table phone column
        Schema::table('user', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop user table phone column
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
