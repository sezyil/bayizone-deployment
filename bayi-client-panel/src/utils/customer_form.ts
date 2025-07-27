import { ICompanyCustomerForm, ICompanyCustomerFormErrors } from "/@src/shared/form/interface-company-customer"

export const resetFormData = (): ICompanyCustomerForm => ({
    authorized_name: '',
    tax_identity_no: null,
    tax_office: null,
    group: 'POTENTIAL',
    company_name: null,
    phone: '',
    fax: null,
    email: '',
    address: '',
    country_id: 0,
    state_id: 0,
    city_id: 0,
    postcode: '',
    language: 'tr',
    type: 1,
    status: true
})

export const resetFormErrors = (): ICompanyCustomerFormErrors => ({
    authorized_name: [],
    tax_identity_no: [],
    group: [],
    tax_office: [],
    company_name: [],
    phone: [],
    fax: [],
    email: [],
    address: [],
    country_id: [],
    language: [],
    state_id: [],
    city_id: [],
    postcode: [],
    type: [],
    status: [],
});