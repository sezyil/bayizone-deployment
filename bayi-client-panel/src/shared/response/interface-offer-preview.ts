import { EnumOfferStatus } from "../form/interface-offer"

export interface IOfferPreviewResponse {
    company_customer_id: string
    total_price: string
    total_tax: string
    total_discount: string
    grand_total: string
    offer_date: string
    offer_due_date: string
    offer_no: string
    currency: string
    note: any
    billing_address: string
    billing_city: string
    billing_state: string
    billing_country: string
    billing_zip_code: any
    payment_bank_name: string
    payment_branch_name: string
    payment_account_name: string
    payment_account_number: string
    payment_iban: string
    payment_swift_code: string | null
    contact_person: any
    contact_email: any
    contact_phone: any
    whatsapp_notification_date: any
    mail_notification_date: any
    password: any
    is_request: number
    international_order: boolean
    status: EnumOfferStatus
    created_at: string
    updated_at: string
    lines: IOfferPreviewLineResponse[]
    company_customer: IOfferPreviewCompanyCustomerResponse
    customer: IOfferPreviewCustomerResponse
}

export interface IOfferPreviewLineResponse {
    id: string
    product_id: number
    product_name: string
    product_code: string
    product_unit: string
    product_image_url: any
    unit_name: string,
    quantity: string
    unit_price: string
    tax_rate: string
    unit_discount_price: string
    unit_discount_rate: string
    total_discount_price: string
    total_price: string
    grand_total: string
    note: any
}

export interface IOfferPreviewCompanyCustomerResponse {
    authorized_name: string
    tax_office: string
    tax_identity_no: string
    company_name: string
    phone: string
    fax: string
    email: string
    address: string
    country_id: string
    state_id: string
    city_id: string
    postcode: string
    type: number
}

export interface IOfferPreviewCustomerResponse {
    firm_name: string
    tax_no: string
    tax_administration: string
    address: string
    country: string
    state: string
    city: string
    postcode: string
    email: string
    phone: string
}

