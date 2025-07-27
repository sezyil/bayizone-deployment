<?php

namespace App\Enums\Shipment;

enum CustomerShipmentStatusEnum: string
{
    case DRAFT = 'DRAFT';
    case PENDING = 'PENDING';
    case SHIPPED = 'SHIPPED';
    case DELIVERED = 'DELIVERED';
    case CANCELED = 'CANCELED';

    public static function description(string $status): string
    {
        return match ($status) {
            self::DRAFT->value => __('statuses.draft'),
            self::PENDING->value => __('statuses.customerShipmentStatus.pending'),
            self::SHIPPED->value => __('statuses.customerShipmentStatus.shipped'),
            self::DELIVERED->value => __('statuses.customerShipmentStatus.delivered'),
            self::CANCELED->value => __('statuses.customerShipmentStatus.canceled'),
            default => __('statuses.unknown'),
        };
    }

    //all
    public static function all(): array
    {
        return [
            self::DRAFT->value,
            self::PENDING->value,
            self::SHIPPED->value,
            self::DELIVERED->value,
            self::CANCELED->value,
        ];
    }
}
