<?php

namespace App\Enums;

enum CustomerOrderLineStatusEnum: string
{
    case PENDING = 'PENDING'; // bekliyor
    case IN_PRODUCTION = 'IN_PRODUCTION'; // üretimde
    case CANCELED = 'CANCELED'; // iptal edildi firma tarafından
    case REJECTED = 'REJECTED'; // reddedildi (iptal edildi) müşteri tarafından
    case PARTIALLY_SHIPPED = 'PARTIALLY_SHIPPED'; // kısmen gönderildi
    case READY_TO_SHIP = 'READY_TO_SHIP'; // sevk edilmeye hazır
    case SHIPPED = 'SHIPPED'; // gönderildi
    case DELIVERED = 'DELIVERED'; // teslim edildi
    case COMPLETED = 'COMPLETED'; // tamamlandı

    //açıklamalar
    public static function description(string $status): string
    {
        switch ($status) {
            case self::PENDING->value:
                return __('statuses.customerOrderLineStatus.pending');
            case self::IN_PRODUCTION->value:
                return __('statuses.customerOrderLineStatus.in_production');
            case self::REJECTED->value:
                return __('statuses.customerOrderLineStatus.rejected');
            case self::CANCELED->value:
                return __('statuses.customerOrderLineStatus.canceled');
            case self::PARTIALLY_SHIPPED->value:
                return __('statuses.customerOrderLineStatus.partially_shipped');
            case self::SHIPPED->value:
                return __('statuses.shipped');
            case self::READY_TO_SHIP->value:
                return __('statuses.customerOrderLineStatus.ready_to_ship');
            case self::DELIVERED->value:
                return __('statuses.customerOrderLineStatus.delivered');
            case self::COMPLETED->value:
                return __('statuses.customerOrderLineStatus.completed');
            default:
                return __('statuses.unknown');
        }
    }

    //get all
    public static function all(): array
    {
        return [
            self::PENDING->value,
            self::IN_PRODUCTION->value,
            self::CANCELED->value,
            self::REJECTED->value,
            self::PARTIALLY_SHIPPED->value,
            self::READY_TO_SHIP->value,
            self::SHIPPED->value,
            self::DELIVERED->value,
            self::COMPLETED->value
        ];
    }
}
