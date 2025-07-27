<?php

namespace App\Enums;

enum BatchProcessesTypesEnum: string
{
        //product batch
    case PRODUCT = 'PRODUCT';
    case MAIL = 'MAIL';

    //get all
    public static function allValues(): array
    {
        return [
            self::PRODUCT->value,
            self::MAIL->value,
        ];
    }

    //translate permission type
    public static function translate(string $type): string
    {
        switch ($type) {
            case self::PRODUCT->value:
                return 'Ürün';
            case self::MAIL->value:
                return 'Mail';
            default:
                return '';
        }
    }
}
