import { IOfferFormDetail } from "../form/interface-offer"
import { IProductVariantColorData, IProductVariantDimensionData } from "../product/interface-product"


export interface OfferRequestFormResponse {
    offer: OfferRequestOffer
    bank_accounts: OfferRequestBankAccount[]
    company_customer: OfferRequestCompanyCustomer
    cc_warehouses: OfferRequestCcWarehouse[]
}

export interface OfferRequestOffer {
    id: string
    customer_id: string
    company_customer_id: string
    global_note: any
    status: string
    created_at: string
    updated_at: string
    currency: IOfferFormDetail['currency']
    lines: OfferRequestLine[]
}

export interface OfferRequestLine {
    id: number
    product_id: number
    product_name: string
    default_price: number
    image: string
    model: string
    quantity: number
    note: string
    options: OfferRequestOption[]
    color: OfferRequestLineVariant[] | null
    dimension: OfferRequestLineVariant[] | null
}


export interface OfferRequestLineVariant {
    id: number
    type: string
    product_variant_id: string
    product_variant_value_id: string
    variant_id: string
    variant_value_id: string
}

export interface OfferRequestBankAccount {
    id: string
    customer_id: string
    bank_name: string
    branch_name: string
    account_name: string
    account_number: string
    iban: string
    swift_code: string
    currency: string
    status: number
    created_at: string
    updated_at: string
}

export interface OfferRequestOption {
    id: number
    product_option_value_id: number
}

export interface OfferRequestCompanyCustomer {
    id: string
    customer_id: string
    authorized_name: string
    tax_office: string
    tax_identity_no: string
    company_name: string
    phone: string
    fax: any
    email: string
    address: string
    country_id: number
    state_id: number
    city_id: number
    postcode: any
    type: number
    status: boolean
    created_at: string
    updated_at: string
}

export interface OfferRequestCcWarehouse {
    id: number
    customer_id: string
    company_customer_id: string
    name: string
    address: string
    phone: string
    email: string
    contact_person: string
    contact_person_phone: string
    contact_person_email: string
    city_id: number
    state_id: number
    country_id: number
    zip_code: string
    created_at: string
    updated_at: string
}
