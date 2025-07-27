<?php

namespace App\Enums;

enum CustomerOfferStatusEnum: string
{
    case DRAFT = 'DRAFT'; // taslak
    case PENDING = 'PENDING'; // onay bekliyor
    case APPROVED = 'APPROVED'; // onaylandı
    case REJECTED = 'REJECTED'; // reddedildi (iptal edildi) müşteri tarafından
    case EXPIRED = 'EXPIRED'; // süresi doldu

    case REVISED = 'REVISED'; // revize edildi

    case CANCELED = 'CANCELED'; // iptal edildi firma tarafından

    case CLOSED = 'CLOSED'; // kapatıldı (siparişe dönüştü)

    //açıklamalar
    public static function description(string $status): string
    {
        switch ($status) {
            case self::DRAFT->value:
                return "Taslak";
            case self::PENDING->value:
                return "Onay Bekliyor";
            case self::APPROVED->value:
                return "Onaylandı";
            case self::REJECTED->value:
                return "Reddedildi";
            case self::EXPIRED->value:
                return "Süresi Doldu";
            case self::REVISED->value:
                return "Revize Edildi";
            case self::CANCELED->value:
                return "İptal Edildi";
            case self::CLOSED->value:
                return "Kapatıldı";
            default:
                return "Bilinmeyen";
        }
    }

    //get all
    public static function all(): array
    {
        return [
            self::DRAFT->value,
            self::PENDING->value,
            self::APPROVED->value,
            self::REJECTED->value,
            self::EXPIRED->value,
            self::REVISED->value,
            self::CANCELED->value,
            self::CLOSED->value,
        ];
    }
}
