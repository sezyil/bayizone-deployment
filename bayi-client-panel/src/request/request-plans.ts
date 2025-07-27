import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";
import { AxiosInstance } from 'axios';

export interface ICheckoutGetResponse {
    plan: ICheckoutPlanResponse
    customer: ICheckoutCustomerResponse
}

export interface ICheckoutPlanResponse {
    id: string
    name: string
    selected_price_id: number
    selected_price_price: number
    selected_price_duration: number
    selected_price_tax: number
    selected_price_subtotal: number
    selected_price_total: number
}

export interface ICheckoutCustomerResponse {
    firm_name: string
    tax_no: string
    tax_administration: string
    address: string
    country_id: number
    country: string
    state_id: number
    state: string
    city_id: number
    city: string
    postcode: any
    email: string
    phone: string
}


export default class PlansApi {
    private api;
    private _uri;
    constructor() {
        this.api = useApi();
        this._uri = API_URIS.PLANS;
    }

    async list() {
        return this.api.get(this._uri);
    }

    async get(id: string, price_id: string) {
        return this.api.get(this._uri + '/' + id + '?price_id=' + price_id);
    }

    async purchase(id: string, price_id: any) {
        return this.api.post(this._uri + '/' + id + '/purchase', {
            price_id: price_id
        });
    }
}