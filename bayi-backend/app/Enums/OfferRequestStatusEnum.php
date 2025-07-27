<?php

namespace App\Enums;

enum OfferRequestStatusEnum: string
{
    case PENDING = 'PENDING';
    case ACCEPTED = 'ACCEPTED';
    case REJECTED = 'REJECTED';

    public static function getValues(): array
    {
        return [
            self::PENDING->value,
            self::ACCEPTED->value,
            self::REJECTED->value,
        ];
    }

}
