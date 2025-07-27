<?php

namespace App\Enums;

enum PermissionTypes: string
{
    case attribute = 'attribute';
    case attribute_group = 'attribute_group';
    case attribute_value = 'attribute_value';
    case category = 'category';
    case customer = 'customer';
    case customer_bank_account = 'customer_bank_account';
    case customer_offer = 'customer_offer';
    case company_customer = 'company_customer';
    case company_customer_bank_account = 'company_customer_bank_account';
    case company_customer_warehouse = 'company_customer_warehouse';
    case product = 'product';
    case user = 'user';
    case unit = 'unit';

    case offer_request = 'offer_request';

    case permission = 'permission';

    case transaction = 'transaction';

    case customer_order = 'customer_order';

    case payment = 'payment';

    case variant = 'variant';

    public static function allValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    //translate permission type
    public static function translate(string $type): string
    {
        $vals = self::allValues();
        switch ($type) {
            case self::attribute->value:
                return 'Özellik';
            case self::attribute_group->value:
                return 'Özellik Grubu';
            case self::attribute_value->value:
                return 'Özellik Değeri';
            case self::category->value:
                return 'Kategori';
            case self::customer->value:
                return 'Şirket';
            case self::customer_bank_account->value:
                return 'Banka Hesabı';
            case self::customer_offer->value:
                return 'Müşteri Teklif';
            case self::company_customer->value:
                return 'Müşteri';
            case self::company_customer_bank_account->value:
                return 'Müşteri Banka Hesabı';
            case self::company_customer_warehouse->value:
                return 'Müşteri Depo';
            case self::product->value:
                return 'Ürün';
            case self::user->value:
                return 'Kullanıcı';
            case self::offer_request->value:
                return 'Teklif İsteği';
            case self::unit->value:
                return 'Birim';
            case self::permission->value:
                return 'İzin';
            case self::transaction->value:
                return 'Finans Hareketleri';
            case self::customer_order->value:
                return 'Sipariş';
            case self::payment->value:
                return 'Ödeme';
            case self::variant->value:
                return 'Varyant';
            default:
                return '';
        }
    }
}
