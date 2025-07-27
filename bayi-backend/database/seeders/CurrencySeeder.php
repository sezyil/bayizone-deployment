<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // insert default currencies
        DB::statement("INSERT INTO currencies (title, code, `sign` , symbol_right, `default`, rate, status, created_at, updated_at)
        VALUES
            ('Türk Lirası', 'tl', '₺', 1, 1, 1.0000, 1, NOW(), NOW()),
            ('ABD Doları', 'usd', '$', 0, 0, 1.0000, 1, NOW(), NOW()),
            ('Euro', 'euro', '€', 0, 0, 1.0000, 1, NOW(), NOW()),
            ('İngiliz Sterlini', 'gbp', '£', 0, 0, 1.0000, 1, NOW(), NOW());");
    }
}
