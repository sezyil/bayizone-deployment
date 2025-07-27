export enum ECompanyCustomerType {
    Company = 1,
    Person = 2
}

export type ECompanyCustomerGroup = 'POTENTIAL' | 'CUSTOMER' | 'SUPPLIER';
//description group
export function getCompanyCustomerGroupDescription(group: ECompanyCustomerGroup): string {
    switch (group) {
        case 'POTENTIAL':
            return 'Potansiyel Müşteri';
        case 'CUSTOMER':
            return 'Müşteri';
        case 'SUPPLIER':
            return 'Tedarikçi';
        default:
            return 'Bilinmeyen';
    }
}

export interface ICompanyCustomerForm {
    authorized_name: string;
    tax_identity_no: string | null;
    group: ECompanyCustomerGroup;
    tax_office: string | null;
    company_name: string | null;
    phone: string;
    fax: string | null;
    email: string;
    address: string;
    country_id: number;
    state_id: number;
    city_id: number;
    postcode: string;
    language: 'tr' | 'en';
    /** @description 1: tüzel kişi, 2: gerçek kişi */
    type: ECompanyCustomerType;
    status: boolean;
}

//errors interface for company customer
export interface ICompanyCustomerFormErrors {
    authorized_name: string[];
    tax_identity_no: string[];
    group: string[];
    tax_office: string[];
    company_name: string[];
    phone: string[];
    fax: string[];
    email: string[];
    address: string[];
    country_id: string[];
    state_id: string[];
    city_id: string[];
    postcode: string[];
    language: string[];
    type: string[];
    status: string[];
}