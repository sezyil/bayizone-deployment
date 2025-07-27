export interface ICompanyCustomerWarehouseForm {
    name: string
    address: string
    phone: string
    email: string
    contact_person: string
    contact_person_phone: string
    contact_person_email: string
    country_id: number
    state_id: number
    city_id: number
    zip_code: string
}

export interface ICompanyCustomerWarehouseFormErrors {
    name: string[]
    address: string[]
    phone: string[]
    email: string[]
    contact_person: string[]
    contact_person_phone: string[]
    contact_person_email: string[]
    country_id: string[]
    state_id: string[]
    city_id: string[]
    zip_code: string[]
}