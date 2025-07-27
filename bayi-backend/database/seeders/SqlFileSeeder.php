<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SqlFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //!!dont call from DatabaseSeeder because it will be called twice!!
        //!!it called in migration!!
        $sql = file_get_contents(database_path('seeders/sql/countries.sql'));
        \DB::unprepared($sql);

        $sql = file_get_contents(database_path('seeders/sql/states.sql'));
        \DB::unprepared($sql);

        $sql = file_get_contents(database_path('seeders/sql/cities.sql'));
        \DB::unprepared($sql);
    }
}
