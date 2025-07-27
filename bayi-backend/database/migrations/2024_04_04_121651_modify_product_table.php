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
        Schema::table('products', function (Blueprint $table) {
            //volume
            $table->decimal('volume', 15, 2)->after('weight')->comment('Hacim (m3)');
            //package
            $table->decimal('package', 10, 2)->after('volume')->comment('Paket Adedi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('volume');
            $table->dropColumn('package');
        });
    }
};
