<?php

namespace App\Enums;


enum CompanyCustomerGroupEnum: string
{
    case POTENTIAL = "POTENTIAL";
    case CUSTOMER = "CUSTOMER";
    case SUPPLIER = "SUPPLIER";


    public static function description(string $status): string
    {
        switch ($status) {
            case self::POTENTIAL->value:
                return "Potansiyel Müşteri";
            case self::CUSTOMER->value:
                return "Müşteri";
            case self::SUPPLIER->value:
                return "Tedarikçi";
            default:
                return "Bilinmeyen";
        }
    }

    public static function getValues(): array
    {
        return [
            self::POTENTIAL->value,
            self::CUSTOMER->value,
            self::SUPPLIER->value
        ];
    }

}
