<?php

use App\Models\System\Language;
use App\Models\System\Unit;
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
        Schema::create('unit_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('language', Language::CODE_LENGTH)->default('tr');
            $table->timestamps();

            //fk
            $table->foreign('language')->references('code')->on('languages')->cascadeOnDelete();
        });

        $short_names = [
            'kg' => [
                'tr' => 'Kilogram',
                'en' => 'Kilogram',
            ],
            'adet' => [
                'tr' => 'Adet',
                'en' => 'Piece',
            ],
            'lt' => [
                'tr' => 'Litre',
                'en' => 'Liter',
            ],
            'm' => [
                'tr' => 'Metre',
                'en' => 'Meter',
            ],
        ];

        foreach ($short_names as $short_name => $names) {
            $find = Unit::where('short_name', $short_name)->first();
            if ($find) {
                foreach ($names as $language => $name) {
                    $find->descriptions()->create([
                        'name' => $name,
                        'language' => $language,
                    ]);
                }
            }
        }

        //customer units
        $customer_units = Unit::where('is_system', false)->get();
        foreach ($customer_units as $unit) {
            $unit->descriptions()->create([
                'name' => $unit->name,
                'language' => 'tr',
            ]);
            $unit->descriptions()->create([
                'name' => $unit->name,
                'language' => 'en',
            ]);
        }

        //drop from unit table name column
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_descriptions');

        //add name column to unit table
        Schema::table('units', function (Blueprint $table) {
            $table->string('name');
        });
    }
};
