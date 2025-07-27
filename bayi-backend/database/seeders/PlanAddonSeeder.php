<?php

namespace Database\Seeders;

use App\Enums\SubscriptionTypesEnum;
use App\Models\Plan;
use App\Models\PlanAddon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanAddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boolAddons = [
            'sales_management',
            'simple_accounting',
            'online_store',
        ];

        foreach ($boolAddons as $addon) {
            PlanAddon::create([
                'name' => SubscriptionTypesEnum::description($addon),
                'type' => $addon,
                'is_boolean' => true,
                'quantity' => 1,
                'price' => 100,
                'status' => 1
            ]);
        }

        $intAddons = [
            'user_count',
            'product_count',
            'provider_panel_count',
        ];

        foreach ($intAddons as $addon) {
            $quantity = 1;
            $is_bulk = false;
            $bulk_quantity = 0;
            switch ($addon) {
                case 'user_count':
                    $is_bulk = true;
                    $bulk_quantity = 5;
                    break;
                case 'product_count':
                    $is_bulk = true;
                    $bulk_quantity = 50;
                    break;
                case 'provider_panel_count':
                    $is_bulk = true;
                    $bulk_quantity = 5;
                    break;
            }
            PlanAddon::create([
                'name' => $bulk_quantity . ' ' . SubscriptionTypesEnum::description($addon),
                'type' => $addon,
                'is_boolean' => false,
                'quantity' => $quantity,
                'is_bulk' => $is_bulk,
                'bulk_quantity' => $bulk_quantity,
                'price' => 100,
                'status' => 1
            ]);
        }
    }
}
