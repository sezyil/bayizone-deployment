<?php

namespace App\Enums;

enum OrderPaymentMethods: string
{
    case CREDIT_CARD = 'CREDIT_CARD';
    case BANK_TRANSFER = 'BANK_TRANSFER';

    public static function description(string $value): string
    {
        switch ($value) {
            case self::CREDIT_CARD->value:
                return 'Kredi Kartı';
            case self::BANK_TRANSFER->value:
                return 'Banka Transferi';
            default:
                return 'Seçim Yapılmadı';
        }
    }

    public static function all(): array
    {
        return [
            self::CREDIT_CARD->value,
            self::BANK_TRANSFER->value,
        ];
    }
}
