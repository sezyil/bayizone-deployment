<?php

namespace App\Enums;

enum CustomerOrderStatusEnum: string
{
    case DRAFT = 'DRAFT'; // taslak
    case APPROVED = 'APPROVED'; // İŞLEMDE
    case IN_PRODUCTION = 'IN_PRODUCTION'; // üretimde
    case REJECTED = 'REJECTED'; // reddedildi (iptal edildi) müşteri tarafından
    case CANCELED = 'CANCELED'; // iptal edildi firma tarafından
    case PARTIALLY_SHIPPED = 'PARTIALLY_SHIPPED'; // kısmen gönderildi
    case SHIPPED = 'SHIPPED'; // gönderildi
    case READY_TO_SHIP = 'READY_TO_SHIP'; // yolda
    case DELIVERED = 'DELIVERED'; // teslim edildi
    case COMPLETED = 'COMPLETED'; // tamamlandı

    //açıklamalar
    public static function description(string $status): string
    {
        switch ($status) {
            case self::DRAFT->value:
                return __('statuses.customerOrderStatus.draft');
            case self::APPROVED->value:
                return __('statuses.customerOrderStatus.approved');
            case self::IN_PRODUCTION->value:
                return __('statuses.customerOrderStatus.in_production');
            case self::REJECTED->value:
                return __('statuses.customerOrderStatus.rejected');
            case self::CANCELED->value:
                return __('statuses.customerOrderStatus.canceled');
            case self::PARTIALLY_SHIPPED->value:
                return __('statuses.customerOrderStatus.partially_shipped');
            case self::READY_TO_SHIP->value:
                return __('statuses.customerOrderStatus.ready_to_ship');
            case self::SHIPPED->value:
                return __('statuses.shipped');
            case self::DELIVERED->value:
                return __('statuses.customerOrderStatus.delivered');
            case self::COMPLETED->value:
                return __('statuses.customerOrderStatus.completed');
            default:
                return __('statuses.unknown');
        }
    }

    //get all
    public static function all(): array
    {
        return [
            self::DRAFT->value,
            self::APPROVED->value,
            self::IN_PRODUCTION->value,
            self::REJECTED->value,
            self::CANCELED->value,
            self::PARTIALLY_SHIPPED->value,
            self::SHIPPED->value,
            self::READY_TO_SHIP->value,
            self::DELIVERED->value,
            self::COMPLETED->value,
        ];
    }
}
