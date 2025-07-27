<?php

use App\Models\System\Language;
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
        Schema::create('variant_value_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('variant_value_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('language', Language::CODE_LENGTH)->default('tr');
            $table->timestamps();

            //fk
            $table->foreign('language')->references('code')->on('languages')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_value_descriptions');
    }
};
