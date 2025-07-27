<?php

namespace App\Enums;


enum CustomerTransactionsPaymentTypeEnum: string
{
    case CASH = 'CASH';
    case CREDIT_CARD = 'CREDIT_CARD';
    case BANK_TRANSFER = 'BANK_TRANSFER';
    case CHECK = 'CHECK';
    case OTHER = 'OTHER';

    //description
    public static function description(string $type): string
    {
        switch ($type) {
            case self::CASH->value:
                return "Nakit";
            case self::CREDIT_CARD->value:
                return "Kredi Kartı";
            case self::BANK_TRANSFER->value:
                return "Banka Transferi";
            case self::CHECK->value:
                return "Çek";
            case self::OTHER->value:
                return "Diğer";
            default:
                return "Bilinmeyen";
        }
    }

    //all
    public static function all(): array
    {
        return [
            self::CASH->value,
            self::CREDIT_CARD->value,
            self::BANK_TRANSFER->value,
            self::CHECK->value,
            self::OTHER->value,
        ];
    }
}
