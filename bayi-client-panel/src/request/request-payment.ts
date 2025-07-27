import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";
import { EnumCustomerOrderStatus } from '../shared/form/interface-customer-order';

export interface IRequestPayResponse {
    data: IRequestPayDataResponse
}

export type TypeOrderPaymentMethod = 'CREDIT_CARD' | 'BANK_TRANSFER'
export function getOrderPaymentMethods() {
    return [
        { value: 'CREDIT_CARD', text: 'Kredi Kartı' },
        { value: 'BANK_TRANSFER', text: 'Havale/EFT' }
    ] as { value: TypeOrderPaymentMethod, text: string }[]
}
type LineType = 'subscription' | 'addon'

export interface IRequestPayDataResponse {
    id: string
    order_no: string
    total: string
    is_paid: boolean
    payment_method: TypeOrderPaymentMethod | null
    payment_date: any
    is_active: boolean
    created_at: string
    updated_at: string
    invoice_firm_name: string
    invoice_tax_no: string
    invoice_tax_administration: string
    invoice_address: string
    invoice_country_id: number
    invoice_country: string
    invoice_state_id: number
    invoice_state: string
    invoice_city_id: number
    invoice_city: string
    invoice_postcode: string
    invoice_email: string
    invoice_phone: any
    tax_amount: string
    subtotal: string
    transfer_account_name: string | null
    transfer_bank_name: string | null
    transfer_reference_no: string | null
    transfer_date: string | null
    lines: IRequestPayLine[]
    converted_total: string
    converted_tax_amount: string
    converted_subtotal: string
    waiting_transfer_approve: boolean
    coupon_id: string | null
    coupon_code: string | null
    discount_amount: number
    discount_percentage: number
    converted_discount_amount: number
}

export interface IRequestPayLine {
    id: number
    type: LineType
    name: string
    price: string
    item_id: string
    item_data: IRequestPayItemData
    tax_rate: string
    tax_amount: string
    quantity: string
    subtotal: string
    total: string
}

export interface IRequestPayItemData {
    type?: string
    amount?: number
    quantity?: number
    is_boolean?: number
    duration?: number
}

export interface IRequestPayPurchaseResponse {
    success: boolean
    msg: string
    data: {
        payment_disallowed: boolean
        iyzico_redirect: string
    }
}

export interface IRequestPayBankTransferDetails {
    data: {
        iban: string
        bank_name: string
        account_name: string
    }
}

class PaymentApi {
    private api;
    public _uri;

    constructor() {
        this.api = useApi();
        this._uri = API_URIS.PAYMENTS;
    }

    async list(searchQuery = '') {
        let uri = this._uri + (searchQuery !== '' ? '?q=' + searchQuery : '');
        return this.api.get(uri);
    }

    async get(offer_id: string) {
        let uri = `${this._uri}/${offer_id}/edit`;
        return this.api.get(uri);
    }

    async update(data: any, offer_id: string) {
        let uri = `${this._uri}/${offer_id}`;
        return this.api.put(uri, data);
    }

    async purchase(id: string, data: any) {
        let uri = `${this._uri}/${id}/purchase`;
        return this.api.post<IRequestPayPurchaseResponse>(uri, data);
    }

    //show
    async show(id: string) {
        let uri = `${this._uri}/${id}`;
        return this.api.get(uri);
    }

    async preview(offer_id: string) {
        let uri = `${this._uri}/${offer_id}/preview`;
        return this.api.get(uri);
    }

    async cancel(offer_id: string) {
        let uri = `${this._uri}/${offer_id}/cancel`;
        return this.api.patch(uri);
    }

    async changePaymentMethod(id: string, payment_method: TypeOrderPaymentMethod | null) {
        let uri = `${this._uri}/${id}/payment_method`;
        return this.api.patch(uri, { payment_method });
    }

    async getBankTransferDetails() {
        let uri = `${this._uri}/available_bank_transfer`;
        return this.api.get<IRequestPayBankTransferDetails>(uri);
    }

    /**
     * Used for bank transfer payment method
     * @param id 
     * @param data 
     * @returns 
     */
    async sendPaymentNotification(id: string, data: {
        transfer_account_name: string,
        transfer_bank_name: string,
        transfer_reference_no: string,
        transfer_date: string
    }) {
        let uri = `${this._uri}/${id}/payment_notification`;
        return this.api.patch<{
            success: boolean
            msg: string
        }>(uri, data);
    }

    //coupon 
    async registerCoupon(id: string, coupon: string) {
        let uri = `${this._uri}/${id}/coupon`;
        return this.api.patch(uri, { coupon });
    }
}

export default PaymentApi;