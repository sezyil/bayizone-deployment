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
        Schema::table('variants', function (Blueprint $table) {
            $table->softDeletes();
            //drop unique "type"
            $table->dropUnique('variants_type_unique');
        });

        Schema::table('variant_values', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->dropSoftDeletes();
            //$table->unique('type');
        });

        Schema::table('variant_values', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
