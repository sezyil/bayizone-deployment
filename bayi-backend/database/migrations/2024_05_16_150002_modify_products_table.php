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
            //modify height,width,length columns to integer + default 0
            $table->integer('height')->default(0)->change();
            $table->integer('width')->default(0)->change();
            $table->integer('length')->default(0)->change();

            //+ softDeletes
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //decimal 15,2
            $table->decimal('height', 15, 2)->change();
            $table->decimal('width', 15, 2)->change();
            $table->decimal('length', 15, 2)->change();

            $table->dropSoftDeletes();
        });
    }
};
