<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'type' => 'DIMENSION',
            ],
            [
                'type' => 'COLOR',
            ]
        ];

        foreach ($data as $item) {
            $variant = \App\Models\Variant::create([
                'type' => $item['type'],
                'is_active' => true,
                'is_default' => true,
            ]);
        }
    }
}
