<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('customer_id');
            $table->string('model', 64);
            $table->string('gtin', 15)->nullable();
            $table->string('sku', 64)->nullable();
            $table->string('upc', 12)->nullable();
            $table->string('ean', 14)->nullable();
            $table->string('jan', 13)->nullable();
            $table->string('isbn', 17)->nullable();
            $table->string('mpn', 64)->nullable();

            $table->string('image')->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('quantity')->default(0);
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->tinyInteger('date_available')->default(0);
            $table->decimal('weight', 15, 2)->default(0);
            $table->decimal('length', 15, 2)->default(0);
            $table->decimal('width', 15, 2)->default(0);
            $table->decimal('height', 15, 2)->default(0);

            $table->integer('minimum')->default(0);
            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customer');
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
