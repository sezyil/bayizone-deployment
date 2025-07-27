export interface IProfileCompanyForm {
    authorized_person: string
    firm_name: string
    tax_no: string
    tax_administration: string
    address: string
    postcode: string
    country_id: number
    state_id: number
    city_id: number
    email: string
    phone: string
    fax: string
}

export interface IProfileCompanyFormErrors {
    authorized_person: string[]
    firm_name: string[]
    tax_no: string[]
    tax_administration: string[]
    address: string[]
    postcode: string[]
    country_id: string[]
    state_id: string[]
    city_id: string[]
    email: string[]
    phone: string[]
    fax: string[]
}
