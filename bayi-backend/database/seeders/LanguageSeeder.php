<?php

namespace Database\Seeders;

use App\Models\System\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::create([
            'name' => "Türkçe",
            'code' => "tr",
        ]);
        Language::create([
            'name' => "English",
            'code' => "en",
        ]);
    }
}
