import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";
import { AxiosInstance } from 'axios';
import { ITransactionShow } from '../shared/transaction/transaction-show';

interface ITransactionApiFormInputData {
    company_customer_id: string,
    currency: string,
}

export interface ITransactionApiGetFormInputResponse {
    data: {
        customer_orders: {
            id: string
            order_no: string
            grand_total: number
            formatted_grand_total: string
        }[]
    }
}

export interface ITransactionApiGetBalanceResponse {
    data: {
        balance: number
        formatted_balance: string
        total_debt: number
        formatted_total_debt: string
        total_credit: number
        formatted_total_credit: string
        total_paid_debt: number
        formatted_total_paid_debt: string
        total_paid_credit: number
        formatted_total_paid_credit: string
        total_unpaid_debt: number
        formatted_total_unpaid_debt: string
        total_unpaid_credit: number
        formatted_total_unpaid_credit: string
        total_overdue_debt: number
        formatted_total_overdue_debt: string
        total_overdue_credit: number
        formatted_total_overdue_credit: string
    }
}


export default class TransactionApi {
    private api;
    private _uri;
    constructor() {
        this.api = useApi();
        this._uri = API_URIS.TRANSACTIONS;
    }

    async list() {
        return this.api.get(this._uri);
    }

    async get(id: string) {
        return this.api.get(this._uri + '/' + id + '/edit');
    }

    async show(id: string) {
        return this.api.get<ITransactionShow>(this._uri + '/' + id);
    }

    async getFormInputData(ITransactionApiFormInputData: ITransactionApiFormInputData) {
        return this.api.get<ITransactionApiGetFormInputResponse>(this._uri + '/create', {
            params: ITransactionApiFormInputData
        });
    }

    //balance
    async getBalance(company_customer_id: string, currency: string) {
        return this.api.get<ITransactionApiGetBalanceResponse>(this._uri + '/balance', {
            params: {
                company_customer_id,
                currency
            }
        });
    }

    async add(data: any) {
        return this.api.post(this._uri, data);
    }

    async update(id: string, data: any) {
        return this.api.put(this._uri + '/' + id, data);
    }

    async delete(id: string) {
        return this.api.delete(this._uri + '/' + id);
    }

    async payment(id: string) {
        return this.api.get(this._uri + '/' + id + '/payment');
    }

    async downloadExcel(filters: any) {
        let uri = this._uri + '/export-excel';
        return this.api.get(uri, {
            responseType: 'blob',
            headers: {
                'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            },
            params: filters
        });
    }
}