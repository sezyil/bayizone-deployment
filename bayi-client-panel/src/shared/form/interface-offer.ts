import { AvailableLanguages } from "../common/common-language"
import { IProductList, IProductVariantColorData, IProductVariantDimensionData, ProductVariantType } from "../product/interface-product"
import { CurrencyTypes } from "../response/interface-utils-response"
import { IOfferFormProductModalVariants } from "/@src/components/Offer/Form/ProductCard/OfferFormProductModal.vue"

export interface IOfferFormProduct extends IProductList {
    note: string
    price: number
}

export interface IOfferFormProductVariant {
    id?: string
    type: ProductVariantType;
    name: string;
    value: { [key in AvailableLanguages]: string };
}



export enum EnumOfferStatus {
    DRAFT = "DRAFT",
    PENDING = "PENDING",
    APPROVED = "APPROVED",
    REJECTED = "REJECTED",
    EXPIRED = "EXPIRED",
    REVISED = "REVISED",
    CANCELED = "CANCELED",
    CLOSED = "CLOSED",
}





export const getOfferStatusText = (status: EnumOfferStatus): string => {
    switch (status) {
        case EnumOfferStatus.DRAFT:
            return "Taslak";
        case EnumOfferStatus.PENDING:
            return "Onay Bekliyor";
        case EnumOfferStatus.APPROVED:
            return "Onaylandı";
        case EnumOfferStatus.REJECTED:
            return "Reddedildi";
        case EnumOfferStatus.EXPIRED:
            return "Süresi Doldu";
        case EnumOfferStatus.REVISED:
            return "Revize Edildi";
        case EnumOfferStatus.CANCELED:
            return "İptal Edildi";
        case EnumOfferStatus.CLOSED:
            return "Kapatıldı";
        default:
            return "Bilinmeyen";
    }
}

export interface IOfferForm {
    detail: IOfferFormDetail;
    lines: IOfferFormLine[];
}

export interface IOfferFormDetail {
    company_customer_id: string; // UUID, şirket müşteri kimliği
    total_price: number; // Toplam fiyat, 2 ondalık basamak
    total_tax: number; // Toplam vergi, 2 ondalık basamak
    total_discount: number; // Toplam indirim, 2 ondalık basamak
    grand_total: number; // Toplam tutar, 2 ondalık basamak
    offer_date: string; // Teklif tarihi
    offer_due_date: string; // Teklif son tarihi
    offer_no: string; // Teklif numarası, en fazla 50 karakter
    currency: CurrencyTypes | "0"; // Para birimi, en fazla 10 karakter
    note: string; // Notlar, en fazla 500 karakter
    dealer_note: string; // Bayi notları, en fazla 500 karakter
    billing_address: string; // Fatura adresi, en fazla 191 karakter
    billing_city_id: number; // Fatura şehri kimliği (mediumint)
    billing_state_id: number; // Fatura ilçesi kimliği (mediumint)
    billing_country_id: number; // Fatura ülkesi kimliği (mediumint)
    billing_zip_code: string; // Fatura posta kodu, en fazla 191 karakter
    payment_bank_name: string; // Ödeme banka adı, en fazla 191 karakter
    payment_branch_name: string; // Ödeme şube adı, en fazla 191 karakter
    payment_account_name: string; // Ödeme hesap adı, en fazla 191 karakter
    payment_account_number: string; // Ödeme hesap numarası, en fazla 191 karakter
    payment_iban: string; // Ödeme IBAN, en fazla 191 karakter
    payment_swift_code: string | null; // Ödeme SWIFT kodu, en fazla 191 karakter
    contact_person: string; // İletişim kişisi, en fazla 50 karakter
    contact_email: string; // İletişim e-posta, en fazla 50 karakter
    contact_phone: string; // İletişim telefonu, en fazla 50 karakter
    whatsapp_notification_date: Date | null; // WhatsApp bildirim tarihi veya null
    mail_notification_date: Date | null; // E-posta bildirim tarihi veya null
    password: string; // Şifre, en fazla 191 karakter
    is_request: boolean; // İstek olup olmadığı (true/false)
    is_international: boolean; // Uluslararası olup olmadığı (true/false)
    delivery_type: string; // Teslimat tipi
    payment_type: string; // Ödeme tipi
    status: EnumOfferStatus; // Teklif durumu, OfferStatus enum'una ait değerler
    incoterms: string; // Incoterms, en fazla 191 karakter
}

export interface IOfferFormLineDimensionData {
    product_variant_id: string
}

export interface IOfferFormLineColorData {
    product_variant_id: string
}

export interface IOfferFormLine {
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


export interface IOfferFormLineOptions {
    id: number
    product_option_value_id: number
}

export interface IOfferFormDetailErrors {
    company_customer_id: string[];
    total_price: string[];
    total_tax: string[];
    total_discount: string[];
    grand_total: string[];
    offer_date: string[];
    offer_due_date: string[];
    offer_no: string[];
    currency: string[];
    note: string[];
    dealer_note: string[];
    billing_address: string[];
    billing_city_id: string[];
    billing_state_id: string[];
    billing_country_id: string[];
    billing_zip_code: string[];
    payment_bank_name: string[];
    payment_branch_name: string[];
    payment_account_name: string[];
    payment_account_number: string[];
    payment_iban: string[];
    payment_swift_code: string[];
    contact_person: string[];
    contact_email: string[];
    contact_phone: string[];
    whatsapp_notification_date: string[];
    mail_notification_date: string[];
    password: string[];
    is_request: string[];
    is_international: string[];
    delivery_type: string[];
    payment_type: string[];
    status: string[];
    incoterms: string[];
}

export interface IOfferFormLineErrors {
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

export interface IOfferFormErrors {
    detail: IOfferFormDetailErrors;
    lines: IOfferFormLineErrors[];
}

