<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Libraries\Permissions\PermissionGenerator;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //DONT CALL SQL FILE SEEDER FROM HERE BECAUSE IT WILL BE CALLED TWICE!!
        $this->call([
            LanguageSeeder::class,
            CurrencySeeder::class,
            PlanSeeder::class,
            PlanAddonSeeder::class,
            CategoryDefaultSeeder::class,
            VariantSeeder::class,
        ]);
        PermissionGenerator::generateDefaultPermissionsClient();
    }
}
