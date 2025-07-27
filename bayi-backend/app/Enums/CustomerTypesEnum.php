<?php
namespace App\Enums;

enum CustomerTypesEnum: int
{
    /**
     * Tüzel Kişi
     */
    case LEGAL = 1;

    /**
     * Gerçek Kişi
     */
    case REAL = 2;

    //açıklamalar
    public static function description(int $type): string
    {
        switch ($type) {
            case self::LEGAL:
                return "Tüzel Kişi";
            case self::REAL:
                return "Gerçek Kişi";
            default:
                return "Bilinmeyen";
        }
    }

    //get all
    public static function all(): array
    {
        return [
            self::LEGAL,
            self::REAL
        ];
    }
}
