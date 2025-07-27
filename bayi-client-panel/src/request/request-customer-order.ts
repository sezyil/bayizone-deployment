import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";
import { EnumCustomerOrderStatus } from '../shared/form/interface-customer-order';
import { IOrderLineHistoryNewForm } from '../components/pages/CustomerOrder/Show/COShowOrderLineHistory.vue';

class CustomerOrderApi {
    private api;
    public _uri;

    constructor(company_id?: string) {
        this.api = useApi();
        if (company_id)
            this._uri = API_URIS.CUSTOMERS + `/${company_id}/orders`;
        else
            this._uri = API_URIS.CUSTOMER_ORDERS;
    }

    setUri(company_id: string) {
        let uri = '';
        if (company_id)
            this._uri = API_URIS.CUSTOMERS + `/${company_id}/orders`;
        else
            this._uri = API_URIS.CUSTOMERS
    }

    get tableUri() {
        return API_URIS.CUSTOMERS + '/orders';
    }

    setCompanyId(company_id: string) {
        this.setUri(company_id);
    }

    async list(searchQuery = '') {
        let uri = this._uri + (searchQuery !== '' ? '?q=' + searchQuery : '');
        return this.api.get(uri);
    }

    async get(order_id: string) {
        let uri = API_URIS.CUSTOMERS + '/orders/' + order_id + '/edit';
        return this.api.get(uri);
    }

    async add(data: any, request_id?: string) {
        if (!this._uri) throw new Error('Company id is not set');
        let _uri = this._uri;
        if (request_id) _uri += `?request_id=${request_id}`;
        return this.api.post(_uri, data);
    }

    async update(data: any, order_id: string) {
        let uri = `${this._uri}/${order_id}`;
        return this.api.put(uri, data);
    }

    async history(order_id: string) {
        let uri = `${this._uri}/${order_id}/history`;
        return this.api.get(uri);
    }

    async updateStatus(data: {
        status: string
        note: string
        send_notify: boolean
        managed_by_system?: boolean
    }, order_id: string) {
        let uri = API_URIS.CUSTOMERS + '/orders/' + order_id + '/status';
        return this.api.patch(uri, data);
    }

    async preview(order_id: string) {
        let uri = API_URIS.CUSTOMERS + '/orders/' + order_id;
        return this.api.get(uri);
    }

    async updateLineHistory(order_id: string, formData: IOrderLineHistoryNewForm) {
        let uri = API_URIS.CUSTOMERS + '/orders/' + `${order_id}/lines/${formData.order_line_id}/history`;
        return this.api.patch(uri, formData);
    }

    /* async sendWhatsappNotification(order_id: string) {
        let uri = `${this._uri}/${order_id}/send-whatsapp-notification`;
        return this.api.post(uri);
    } */

    async remove(order_id: string) {
        let uri = `${this._uri}/${order_id}`;
        return this.api.delete(uri);
    }

    async downloadExcel(order_id: string, lang: string) {
        let uri = API_URIS.CUSTOMERS + '/orders/' + order_id + '/export-excel';
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
}

export default CustomerOrderApi;