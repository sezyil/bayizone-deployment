<?php

namespace App\Enums;

enum SubscriptionTypesEnum: string
{
        //user_count
    case USER_COUNT = 'user_count';
    case SALES_MANAGEMENT = 'sales_management';
    case SIMPLE_ACCOUNTING = 'simple_accounting';
    case ONLINE_STORE = 'online_store';
    case PRODUCT_COUNT = 'product_count';
    case PROVIDER_PANEL_COUNT = 'provider_panel_count';

    //description
    public static function description(string $type): string
    {
        switch ($type) {
            case self::USER_COUNT->value:
                return "Yönetim Kullanıcısı";
            case self::SIMPLE_ACCOUNTING->value:
                return "Basit Muhasebe Yönetimi";
            case self::SALES_MANAGEMENT->value:
                return "Satış Yönetimi";
            case self::ONLINE_STORE->value:
                return "Online Mağaza B2B";
            case self::PRODUCT_COUNT->value:
                return "Ürün Yükleme Sayısı";
            case self::PROVIDER_PANEL_COUNT->value:
                return "Bayi Yönetim Paneli Müşterisi";
            default:
                return "Bilinmeyen";
        }
    }

    //to array
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
