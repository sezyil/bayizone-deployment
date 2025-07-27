import { IOfferFormLine, IOfferFormLineErrors } from './../../shared/form/interface-offer';
import { EnumOfferStatus, IOfferForm, IOfferFormDetail, IOfferFormErrors, IOfferFormDetailErrors } from "/@src/shared/form/interface-offer"
const tmpDetailData: IOfferFormDetail = {
    company_customer_id: "",
    total_price: 0,
    total_tax: 0,
    total_discount: 0,
    grand_total: 0,
    offer_date: new Date().toString(),
    offer_due_date: new Date().toString(),
    offer_no: "",
    currency: "0",
    note: "",
    dealer_note: "",
    billing_address: "",
    billing_city_id: 0,
    billing_country_id: 0,
    billing_state_id: 0,
    billing_zip_code: "",
    payment_bank_name: "",
    payment_branch_name: "",
    payment_account_name: "",
    payment_account_number: "",
    payment_iban: "",
    payment_swift_code: "",
    contact_person: "",
    contact_email: "",
    contact_phone: "",
    whatsapp_notification_date: null,
    mail_notification_date: new Date(),
    password: "",
    is_request: true,
    incoterms: "",
    is_international: false,
    delivery_type: "",
    payment_type: "",
    status: EnumOfferStatus.APPROVED,
}

const tmpOfferFormErrors: IOfferFormDetailErrors = {
    company_customer_id: [],
    total_price: [],
    total_tax: [],
    total_discount: [],
    grand_total: [],
    offer_date: [],
    offer_due_date: [],
    offer_no: [],
    currency: [],
    note: [],
    dealer_note: [],
    billing_address: [],
    billing_city_id: [],
    billing_country_id: [],
    billing_state_id: [],
    billing_zip_code: [],
    payment_bank_name: [],
    payment_branch_name: [],
    payment_account_name: [],
    payment_account_number: [],
    payment_iban: [],
    payment_swift_code: [],
    contact_person: [],
    contact_email: [],
    contact_phone: [],
    whatsapp_notification_date: [],
    mail_notification_date: [],
    password: [],
    is_request: [],
    is_international: [],
    delivery_type: [],
    payment_type: [],
    status: [],
    incoterms: [],
}

export const resetOfferForm = (): IOfferForm => ({
    detail: { ...tmpDetailData },
    lines: []
})

export const resetOfferFormErrors = () => {
    let x = { ...tmpOfferFormErrors }
    return {
        detail: x,
        lines: []
    } as IOfferFormErrors
}
export const addOfferFormLine = (): IOfferFormLine => {
    return {
        product_id: 0,
        product_name: "",
        product_code: "",
        product_unit: "",
        product_image_url: "",
        unit_id: 0,
        quantity: 0,
        unit_price: 0,
        tax_rate: 0,
        total_package: 0,
        total_volume: 0,
        unit_package: 0,
        unit_volume: 0,
        unit_discount_price: 0,
        unit_discount_rate: 0,
        total_discount_price: 0,
        total_price: 0,
        grand_total: 0,
        note: "",
        color: null,
        dimension: null,
    }
}

export const addOfferFormLineError = (): IOfferFormLineErrors => ({
    product_id: [],
    product_name: [],
    product_code: [],
    product_unit: [],
    product_image_url: [],
    unit_id: [],
    quantity: [],
    unit_price: [],
    tax_rate: [],
    unit_discount_price: [],
    unit_discount_rate: [],
    total_discount_price: [],
    total_price: [],
    grand_total: [],
    note: [],
    unit_volume: [],
    total_volume: [],
    unit_package: [],
    total_package: [],
    color: [],
    dimension: [],
})

export const variantNameConverter = (name: string) => {
    let str = '';
    switch (name) {
        case 'COLOR':
            str = 'Renk';
            break;
        case 'DIMENSION':
            str = 'Boyut';
            break;
        //def
        default:
            str = name;
            break;
    }
    return str;
}