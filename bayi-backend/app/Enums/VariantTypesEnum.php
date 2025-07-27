<?php

namespace App\Enums;

enum VariantTypesEnum: string
{
    case DIMENSION = 'DIMENSION';
    case COLOR = 'COLOR';

    public static function getDescription(string $type, string $lang): string
    {
        return match ($type) {
            self::DIMENSION->value => match ($lang) {
                'tr' => 'Boyut',
                'en' => 'Dimension',
                default => 'Dimension',
            },
            self::COLOR->value => match ($lang) {
                'tr' => 'Renk',
                'en' => 'Color',
                default => 'Color',
            },
            default => 'Dimension',
        };
    }

    public static function getValues(): array
    {
        return [
            self::DIMENSION->value,
            self::COLOR->value,
        ];
    }
}
