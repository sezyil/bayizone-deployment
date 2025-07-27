<?php

namespace App\Enums;

enum CustomerTransactionsFicheTypeEnum: string
{
    /**
     * @deprecated
     */
    case OFFER = 'OFFER';
    /**
     * @deprecated
     */
    case ORDER = 'ORDER';
    /**
     * FATURA
     */
    case INVOICE = 'INVOICE';
    /**
     * TAHSİLAT
     */
    case RECEIPT = 'RECEIPT';
    /**
     * TEDİYE
     */
    case WAYBILL = 'WAYBILL';
    /**
     * İADE
     */
    case RETURN = 'RETURN';

    //description
    public static function description(string $type): string
    {
        switch ($type) {
            case self::OFFER->value:
                return "Teklif";
            case self::ORDER->value:
                return "Sipariş";
            case self::INVOICE->value:
                return "Fatura";
            case self::RECEIPT->value:
                return "Tahsilat";
            case self::WAYBILL->value:
                return "Tediye";
            case self::RETURN->value:
                return "İade";
            default:
                return "Bilinmeyen";
        }
    }

    //to array
    public static function all(): array
    {
        return [
            self::INVOICE->value,
            self::RECEIPT->value,
            self::WAYBILL->value,
            self::RETURN->value
        ];
    }

    //debt group
    public static function debtGroup(): array
    {
        return [
            self::INVOICE->value,
            self::WAYBILL->value,
            self::RETURN->value
        ];
    }

    //credit group
    public static function creditGroup(): array
    {
        return [
            self::RECEIPT->value,
        ];
    }
}
