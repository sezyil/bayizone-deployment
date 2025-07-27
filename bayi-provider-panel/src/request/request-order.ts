import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";


export interface IRequestOrderDetail {
    id: string
    currency: string
    total_price: string
    total_tax: string
    total_discount: string
    grand_total: string
    order_no: string
    billing_address: string
    is_international: number
    note: any
    created_at: string
    updated_at: string
    status: string
    order_date: string
    billing_firm_name: string
    billing_city_name: string
    billing_country_name: string
    billing_state_name: string
    status_text: string
    lines: IRequestOrderDetailLine[]
    histories: IRequestOrderDetailHistory[]
    company_customer: IRequestOrderDetailCompanyCustomer
}

export interface IRequestOrderDetailLine {
    id: number
    product_id: number
    product_name: string
    product_code: string
    product_unit: string
    product_image_url: string
    unit_id: number
    quantity: string
    unit_price: string
    tax_rate: string
    unit_discount_price: string
    unit_discount_rate: string
    total_discount_price: string
    total_price: string
    grand_total: string
    note: string
}

export interface IRequestOrderDetailHistory {
    id: number
    status: string
    status_text: string
    note: string
    created_at: string
}

export interface IRequestOrderDetailCompanyCustomer {
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
    created_at: string
    updated_at: string
    country_name: string
    state_name: string
    city_name: string
}


export default class OrderApi {
    private api;
    private _uri;
    constructor() {
        this.api = useApi();
        this._uri = API_URIS.ORDERS;
    }

    async list() {
        return this.api.get(this._uri);
    }

    async downloadExcel(order_id: string, lang: string) {
        let uri = API_URIS.ORDERS + '/' + order_id + '/export-excel';
        return this.api.get(uri, {
            responseType: 'blob',
            headers: {
                'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            },
            params: {
                lang: lang
            }
        });
    }

    async get(id: string) {
        return this.api.get(`${this._uri}/${id}`);
    }
}