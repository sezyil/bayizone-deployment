import { IProductList, IProductVariantColorData, IProductVariantDimensionData } from "../product/interface-product"
import { CurrencyTypes } from "../response/interface-utils-response"
import { IOfferFormProductModalVariants } from "/@src/components/Offer/Form/ProductCard/OfferFormProductModal.vue"

export interface ICustomerOrderFormProduct extends IProductList {
    note: string
    price: number
}

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

export interface ICustomerOrderForm {
    detail: ICustomerOrderFormDetail;
    lines: ICustomerOrderFormLine[];
}

export interface ICustomerOrderFormDetail {
    company_customer_id: string; // UUID, bayi kimliği
    currency: CurrencyTypes; // Para birimi
    total_price: number; // Toplam fiyat, 2 ondalık basamak
    total_tax: number; // Toplam vergi, 2 ondalık basamak
    total_discount: number; // Toplam indirim, 2 ondalık basamak
    grand_total: number; // Toplam tutar, 2 ondalık basamak
    billing_address: string; // Fatura adresi, en fazla 191 karakter
    billing_city_id: number; // Fatura şehir kimliği (bigint)
    billing_state_id: number; // Fatura ilçe kimliği (bigint)
    billing_country_id: number; // Fatura ülke kimliği (bigint)
    order_no: string; // Sipariş numarası, en fazla 50 karakter
    note: string; // Notlar, en fazla 500 karakter
    is_international: boolean; // Uluslararası sipariş mi?
    status: EnumCustomerOrderStatus; // Sipariş durumu
    order_date: string; // Sipariş tarihi
    total_volume: number // total_volume
    total_package: number // total_package
    delivery_type: string // delivery_type
    payment_type: string // payment_type
    incoterms: string // incoterms
}

export interface ICustomerOrderFormLine {
    product_id: number; // Ürün kimliği (bigint)
    product_name: string; // Ürün adı, en fazla 50 karakter
    product_code: string; // Ürün kodu, en fazla 50 karakter
    product_unit: string; // Ürün birimi, en fazla 50 karakter
    product_image_url: string; // Ürün resim URL'si, en fazla 191 karakter
    unit_id: number; // Birim kimliği (bigint)
    quantity: number; // Miktar, 2 ondalık basamak
    unit_price: number; // Birim fiyatı, 2 ondalık basamak
    tax_rate: number; // Vergi oranı, 2 ondalık basamak
    unit_discount_price: number; // Birim indirim fiyatı, 2 ondalık basamak
    unit_discount_rate: number; // Birim indirim oranı, 2 ondalık basamak
    total_discount_price: number; // Toplam indirim fiyatı, 2 ondalık basamak
    total_price: number; // Toplam fiyat, 2 ondalık basamak
    grand_total: number; // Toplam tutar, 2 ondalık basamak
    note: string; // Notlar, en fazla 500 karakter
    unit_volume: number; // Birim hacim, 2 ondalık basamak
    total_volume: number; // Toplam hacim, 2 ondalık basamak
    unit_package: number; // Birim paket, 2 ondalık basamak
    total_package: number; // Toplam paket, 2 ondalık basamak
    color: IOfferFormProductModalVariants[] | null
    dimension: IOfferFormProductModalVariants[] | null
}



export interface ICustomerOrderFormDetailErrors {
    company_customer_id: string[];
    currency: string[];
    total_price: string[];
    total_tax: string[];
    total_discount: string[];
    grand_total: string[];
    billing_address: string[];
    billing_city_id: string[];
    billing_state_id: string[];
    billing_country_id: string[];
    note: string[];
    status: string[];
    order_no: string[];
    order_date: string[];
    is_international: string[];
    total_volume: string[];
    total_package: string[];
    delivery_type: string[];
    payment_type: string[];
    incoterms: string[];
}

export interface ICustomerOrderFormLineErrors {
    product_id: string[];
    product_name: string[];
    product_code: string[];
    product_unit: string[];
    product_image_url: string[];
    unit_id: string[];
    quantity: string[];
    unit_price: string[];
    tax_rate: string[];
    unit_discount_price: string[];
    unit_discount_rate: string[];
    total_discount_price: string[];
    total_price: string[];
    grand_total: string[];
    note: string[];
    unit_volume: string[];
    total_volume: string[];
    unit_package: string[];
    total_package: string[];
    color: string[];
    dimension: string[];
}

export interface ICustomerOrderFormErrors {
    detail: ICustomerOrderFormDetailErrors;
    lines: ICustomerOrderFormLineErrors[];
}