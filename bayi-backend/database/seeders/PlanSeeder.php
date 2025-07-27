<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'user_count' => ':count Yönetim Kullanıcısı',
            'product_count' => ':count Ürün Yükleyebilme',
            'provider_panel_count' => ':count Bayi Yönetim Paneli Müşterisi',
            'proforma_invoices' => 'Proforma Fatura Oluşturma',
            'proforma_management' => 'Proforma Fatura Yönetim',
            'sales_management' => 'Satış Yönetimi',
            'simple_accounting' => 'Basit Muhasebe Yönetimi',
            'online_store' => 'Online Mağaza B2B',
            'whatsapp_integration' => 'WhatsApp İle Teklif Gönder',
        ];


        $free = Plan::create([
            'is_active' => true,
            'name' => 'Trial',
            'is_featured' => false,
            'is_trial' => true,
            'trial_days' => 7,
            'sales_management' => true,
            'simple_accounting' => true,
            'online_store' => true,
            'user_count' => 1,
            'product_count' => 10,
            'provider_panel_count' => 0,
            'attributes' => [
                $names['whatsapp_integration'],
                $names['proforma_management'],
                $names['sales_management'],
                $names['online_store'],
                str_replace(':count', 1, $names['user_count']),
                str_replace(':count', 10, $names['product_count']),
                str_replace(':count', 0, $names['provider_panel_count']),
            ],
        ]);



        $starter = Plan::create([
            'is_active' => true,
            'name' => 'Starter',
            'is_featured' => false,
            'is_trial' => false,
            'trial_days' => 0,
            'sales_management' => false,
            'simple_accounting' => false,
            'online_store' => false,
            'product_count' => 250,
            'user_count' => 3,
            'provider_panel_count' => 1,
            'attributes' => [
                $names['whatsapp_integration'],
                $names['proforma_management'],
                $names['proforma_invoices'],
                str_replace(':count', 250, $names['product_count']),
                str_replace(':count', 3, $names['user_count']),
                str_replace(':count', 1, $names['provider_panel_count']),
            ],
        ]);

        $this->createPrices($starter, 81.9, 1);
        $this->createPrices($starter, 500, 12);





        $medium = Plan::create([
            'is_active' => true,
            'name' => 'Medium',
            'is_featured' => true,
            'is_trial' => false,
            'trial_days' => 0,
            'sales_management' => false,
            'simple_accounting' => true,
            'online_store' => false,
            'product_count' => 500,
            'user_count' => 5,
            'provider_panel_count' => 5,
            'attributes' => [
                $names['whatsapp_integration'],
                $names['proforma_management'],
                $names['proforma_invoices'],
                $names['simple_accounting'],
                str_replace(':count', 500, $names['product_count']),
                str_replace(':count', 5, $names['user_count']),
                str_replace(':count', 5, $names['provider_panel_count']),
            ],
        ]);

        $this->createPrices($medium, 81.9, 1);
        $this->createPrices($medium, 650, 12);

        $premium = Plan::create([
            'is_active' => true,
            'name' => 'Premium',
            'is_featured' => false,
            'is_trial' => false,
            'trial_days' => 0,
            'sales_management' => true,
            'simple_accounting' => true,
            'online_store' => true,
            'product_count' => 1000,
            'user_count' => 10,
            'provider_panel_count' => 10,
            'attributes' => [
                $names['whatsapp_integration'],
                $names['proforma_management'],
                $names['proforma_invoices'],
                $names['simple_accounting'],
                $names['sales_management'],
                $names['online_store'],
                str_replace(':count', 100, $names['product_count']),
                str_replace(':count', 10, $names['user_count']),
                str_replace(':count', 10, $names['provider_panel_count']),
            ],
        ]);

        $this->createPrices($premium, 100, 1);
        $this->createPrices($premium, 800, 12);
    }


    private function createPrices($plan, $price, $month)
    {
        $plan->prices()->create([
            'price' => $price,
            'month' => $month,
        ]);
    }
}
