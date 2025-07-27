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
        Schema::create('attribute_group_descriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_group_id');
            $table->string('language', 20)->default('tr');
            $table->string('name', 64);

            $table->timestamps();
            $table->foreign('language')->references('code')->on('languages');
            $table->foreign('attribute_group_id')->references('id')->on('attribute_groups')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_group_descriptions');
    }
};
