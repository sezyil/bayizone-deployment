export interface ICustomerOrderShowResponse {
    detail: Detail
    lines: Line[]
    histories: History[]
}

interface Detail {
    company_customer_id: string
    company_customer_name: string
    total_price: string
    total_tax: string
    total_discount: string
    grand_total: string
    order_date: string
    order_no: string
    currency: string
    note: any
    billing_address: string
    billing_city_id: number
    billing_country_id: number
    billing_state_id: number
    status: string
    status_label: string
    is_international: boolean
    delivery_type: any
    payment_type: any
    total_volume: string
    total_package: string
    managed_by_system: boolean
    incoterms: any
    created_at: string
    updated_at: string
}

interface Line {
    id: number
    product_id: number
    product_name: string
    product_code: string
    product_unit: string
    product_image_url: string
    unit_id: number
    quantity: string
    unit_price: string
    tax_rate: string
    unit_discount_price: string
    unit_discount_rate: string
    total_discount_price: string
    total_price: string
    grand_total: string
    note: string
    status: string
    status_label: string
    unit_volume: string
    unit_package: string
    total_volume: string
    total_package: string
    remaining_quantity: number
    sent_quantity: number
    color: ICustomerOrderShowColorVariant[]
    dimension?: ICustomerOrderShowDimensionVariant[]
    history: LineHistory[]
}

export interface ICustomerOrderShowColorVariant {
    id: number
    customer_order_line_id: number
    type: string
    product_variant_id: string
    product_variant_value_id: string
    variant_id: string
    variant_value_id: string
    variant: Variant
    variant_value: VariantValue
}

export interface ICustomerOrderShowDimensionVariant {
    id: number
    customer_order_line_id: number
    type: string
    product_variant_id: string
    product_variant_value_id: string
    variant_id: string
    variant_value_id: string
    variant: Variant
    variant_value: VariantValueDimension
}

interface Variant {
    id: string
    name: string
    variant_id: string
}

interface VariantValue {
    id: string
    name: string
    variant_value_id: string
}

interface VariantValueDimension extends VariantValue {
    value: {
        width: number
        height: number
        length: number
    }
}


interface History {
    id: number
    status: string
    status_label: string
    note: string
    created_at: string
    notify: boolean
}

interface LineHistory {
    id: number
    customer_order_line_id: number
    status: any
    status_label: any
    note: any
    notify: boolean
    created_at: string
}


export namespace CustomerOrderLineStatus {
    export enum CustomerOrderLineStatusEnum {
        PENDING = 'PENDING', // bekliyor
        IN_PRODUCTION = 'IN_PRODUCTION', // üretimde
        CANCELED = 'CANCELED', // iptal edildi firma tarafından
        REJECTED = 'REJECTED', // reddedildi (iptal edildi) müşteri tarafından
        PARTIALLY_SHIPPED = 'PARTIALLY_SHIPPED', // kısmen gönderildi
        SHIPPED = 'SHIPPED', // gönderildi
        READY_TO_SHIP = 'READY_TO_SHIP', // gönderime hazır
        DELIVERED = 'DELIVERED', // teslim edildi
        COMPLETED = 'COMPLETED', // tamamlandı
    }
    // açıklamalar
    export function description(status: string): string {
        switch (status) {
            case CustomerOrderLineStatusEnum.PENDING:
                return 'Bekliyor';
            case CustomerOrderLineStatusEnum.IN_PRODUCTION:
                return 'Üretimde';
            case CustomerOrderLineStatusEnum.REJECTED:
                return 'Reddedildi';
            case CustomerOrderLineStatusEnum.CANCELED:
                return 'İptal Edildi';
            case CustomerOrderLineStatusEnum.PARTIALLY_SHIPPED:
                return 'Kısmen Gönderildi';
            case CustomerOrderLineStatusEnum.SHIPPED:
                return 'Gönderildi';
            case CustomerOrderLineStatusEnum.READY_TO_SHIP:
                return 'Sevkiyata Hazır';
            case CustomerOrderLineStatusEnum.DELIVERED:
                return 'Teslim Edildi';
            case CustomerOrderLineStatusEnum.COMPLETED:
                return 'Tamamlandı';
            default:
                return 'Bilinmeyen';
        }
    }

    // tüm değerleri al
    export function all(): string[] {
        return [
            CustomerOrderLineStatusEnum.PENDING,
            CustomerOrderLineStatusEnum.IN_PRODUCTION,
            CustomerOrderLineStatusEnum.CANCELED,
            CustomerOrderLineStatusEnum.REJECTED,
            CustomerOrderLineStatusEnum.PARTIALLY_SHIPPED,
            CustomerOrderLineStatusEnum.SHIPPED,
            CustomerOrderLineStatusEnum.READY_TO_SHIP,
            CustomerOrderLineStatusEnum.DELIVERED,
            CustomerOrderLineStatusEnum.COMPLETED
        ];
    }
}
