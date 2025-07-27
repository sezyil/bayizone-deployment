export enum EnumCustomerOrderStatus {
    DRAFT = 'DRAFT',
    APPROVED = 'APPROVED',
    IN_PRODUCTION = 'IN_PRODUCTION',
    REJECTED = 'REJECTED',
    CANCELED = 'CANCELED',
    PARTIALLY_SHIPPED = 'PARTIALLY_SHIPPED',
    READY_TO_SHIP = 'READY_TO_SHIP',
    DELIVERED = 'DELIVERED',
    COMPLETED = 'COMPLETED',
    SHIPPED = 'SHIPPED'
}




export const getCustomerOrderStatusText = (status: EnumCustomerOrderStatus): string => {
    switch (status) {
        case EnumCustomerOrderStatus.DRAFT:
            return "Taslak";
        case EnumCustomerOrderStatus.APPROVED:
            return "İşlemde";
        case EnumCustomerOrderStatus.IN_PRODUCTION:
            return "Üretimde";
        case EnumCustomerOrderStatus.REJECTED:
            return "Reddedildi";
        case EnumCustomerOrderStatus.CANCELED:
            return "İptal Edildi";
        case EnumCustomerOrderStatus.PARTIALLY_SHIPPED:
            return "Kısmen Gönderildi";
        case EnumCustomerOrderStatus.SHIPPED:
            return "Gönderildi";
        case EnumCustomerOrderStatus.READY_TO_SHIP:
            return "Sevk edilmeye hazır";
        case EnumCustomerOrderStatus.DELIVERED:
            return "Teslim Edildi";
        case EnumCustomerOrderStatus.COMPLETED:
            return "Tamamlandı";
        default:
            return "Bilinmeyen";
    }
}

export const globCustomerOrderStatusList = [
    { value: EnumCustomerOrderStatus.DRAFT, title: getCustomerOrderStatusText(EnumCustomerOrderStatus.DRAFT) },
    { value: EnumCustomerOrderStatus.APPROVED, title: getCustomerOrderStatusText(EnumCustomerOrderStatus.APPROVED) },
    { value: EnumCustomerOrderStatus.IN_PRODUCTION, title: getCustomerOrderStatusText(EnumCustomerOrderStatus.IN_PRODUCTION) },
    { value: EnumCustomerOrderStatus.REJECTED, title: getCustomerOrderStatusText(EnumCustomerOrderStatus.REJECTED) },
    { value: EnumCustomerOrderStatus.CANCELED, title: getCustomerOrderStatusText(EnumCustomerOrderStatus.CANCELED) },
    { value: EnumCustomerOrderStatus.PARTIALLY_SHIPPED, title: getCustomerOrderStatusText(EnumCustomerOrderStatus.PARTIALLY_SHIPPED) },
    { value: EnumCustomerOrderStatus.READY_TO_SHIP, title: getCustomerOrderStatusText(EnumCustomerOrderStatus.READY_TO_SHIP) },
    { value: EnumCustomerOrderStatus.DELIVERED, title: getCustomerOrderStatusText(EnumCustomerOrderStatus.DELIVERED) },
    { value: EnumCustomerOrderStatus.COMPLETED, title: getCustomerOrderStatusText(EnumCustomerOrderStatus.COMPLETED) },
    { value: EnumCustomerOrderStatus.SHIPPED, title: getCustomerOrderStatusText(EnumCustomerOrderStatus.SHIPPED) }
] as { value: EnumCustomerOrderStatus, title: string }[]