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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->uuid('customer_id')->nullable();
            $table->string('name', 50);
            $table->string('short_name', 50);
            $table->boolean('is_active')->default(true);
            //is system
            $table->boolean('is_system')->default(false);

            //fk
            $table->foreign('customer_id')->references('id')->on('customer')->cascadeOnDelete();

            $table->timestamps();
        });

        //add default units kg, adet, litre, metre, m2, m3
        DB::table('units')->insert([
            [
                'name' => 'Kilogram',
                'short_name' => 'kg',
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Adet',
                'short_name' => 'adet',
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Litre',
                'short_name' => 'lt',
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Metre',
                'short_name' => 'm',
                'is_active' => true,
                'is_system' => true,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
