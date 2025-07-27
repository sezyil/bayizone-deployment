import { ICustomerOrderShowColorVariant, ICustomerOrderShowDimensionVariant } from "./interface-customer-order-show-response"

export namespace ShipmentAvailableOrdersResponse {
    export interface IResponse {
        order_id: string
        order_no: string
        order_date: string
        items: IItem[]
    }

    export interface IItem {
        line_id: number
        product_id: number
        product_name: string
        product_image: string
        quantity: string
        unit_price: string
        total_price: string
        total_tax: any
        total_discount: any
        grand_total: string
        unit_volume: string
        unit_package: string
        total_volume: string
        total_package: string
        color: string
        dimension?: IDimension
        remaining_quantity: number
        sent_quantity: number
    }

    export interface IDimension {
        width: number
        height: number
        length: number
    }
}



export interface IShipmentListResponse {
    id: string
    customer_id: string
    company_customer_id: string
    company_customer_name: string
    shipment_no: string
    container_no: any
    carrier: any
    status: string
    status_label: string
    note: any
    shipped_at: any
    delivered_at: any
    total_price: string
    total_tax: string
    total_discount: string
    grand_total: string
    is_international: boolean
    currency: string
    currency_label: string
    total_volume: string
    total_package: string
    created_at: string
    updated_at: string
}

export enum CustomerShipmentStatusEnum {
    DRAFT = 'DRAFT',
    PENDING = 'PENDING',
    SHIPPED = 'SHIPPED',
    DELIVERED = 'DELIVERED',
    CANCELED = 'CANCELED',
}

export function customerShipmentStatusEnumDescription(status: CustomerShipmentStatusEnum): string {
    switch (status) {
        case CustomerShipmentStatusEnum.DRAFT:
            return 'Taslak';
        case CustomerShipmentStatusEnum.PENDING:
            return 'Bekliyor';
        case CustomerShipmentStatusEnum.SHIPPED:
            return 'Gönderildi';
        case CustomerShipmentStatusEnum.DELIVERED:
            return 'Teslim Edildi';
        case CustomerShipmentStatusEnum.CANCELED:
            return 'İptal Edildi';
        default:
            return 'Bilinmeyen';
    }
}

export namespace ShipmentDetailResponse {
    export interface IShipmentDetailResponse {
        id: string
        company_customer_id: string
        company_customer_name: string
        shipment_no: string
        container_no: string
        carrier: string
        currency: string
        note: string
        status: string
        status_label: string
        total_price: string
        total_tax: string
        total_discount: string
        grand_total: string
        is_international: boolean
        total_volume: string
        total_package: string
        total_weight: string
        created_at: string
        updated_at: string
        shipped_at: string | null
        delivered_at: string | null
        lines: Line[]
        histories: History[]
    }

    export interface Line {
        id: string
        customer_order_line_id: number
        order_id: string
        order_no: string
        line_no: string
        quantity: number
        unit_volume: string
        unit_package: string
        weight: string
        total_volume: string
        total_package: string
        total_weight: string
        product_name: string
        product_code: string
        product_image: string
        color: ICustomerOrderShowColorVariant[]
        dimension?: ICustomerOrderShowDimensionVariant[]
        unit_price: string
        grand_total: string
    }

    export interface Dimension {
        width: number
        height: number
        length: number
    }

    export interface History {
        id: number
        status: string
        status_label: string
        note: string
        created_at: string
        notify: boolean
    }

}





