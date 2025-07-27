import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";
import { EnumCustomerOrderStatus } from '../shared/form/interface-customer-order';
import { IOrderLineHistoryNewForm } from '../components/pages/CustomerOrder/Show/COShowOrderLineHistory.vue';
import { CurrencyTypes } from '../shared/response/interface-utils-response';

interface IApiShipmentRequestData {
    note: string
    notify: boolean
    date: string
}

class ShipmentsApi {
    private api;
    public _uri;

    constructor() {
        this.api = useApi();
        this._uri = API_URIS.SHIPMENTS;
    }

    get tableUri() {
        return API_URIS.SHIPMENTS;
    }

    async list(searchQuery = '') {
        let uri = this._uri + (searchQuery !== '' ? '?q=' + searchQuery : '');
        return this.api.get(uri);
    }

    async getAvailableOrders(company_id: string, currency_id: CurrencyTypes) {
        let uri = this._uri + '/available_orders';
        return this.api.post(uri, { company_id, currency_id });
    }

    async add(data: any) {
        let uri = this._uri;
        return this.api.post(uri, data);
    }

    async update(shipment_id: string, data: any) {
        let uri = this._uri + '/' + shipment_id;
        return this.api.put(uri, data);
    }

    async delete(shipment_id: string) {
        let uri = this._uri + '/' + shipment_id;
        return this.api.delete(uri);
    }

    async get(shipment_id: string) {
        let uri = this._uri + '/' + shipment_id;
        return this.api.get(uri);
    }

    async editData(shipment_id: string) {
        let uri = this._uri + '/' + shipment_id + '/edit';
        return this.api.get(uri);
    }

    async approve(shipment_id: string) {
        let uri = this._uri + '/' + shipment_id + '/approve';
        return this.api.put(uri);
    }

    async sendShipment(shipment_id: string, data: IApiShipmentRequestData) {
        let uri = this._uri + '/' + shipment_id + '/send';
        return this.api.put(uri, data);
    }

    async deliverShipment(shipment_id: string, data: IApiShipmentRequestData) {
        let uri = this._uri + '/' + shipment_id + '/deliver';
        return this.api.put(uri, data);
    }

    async downloadExcel(shipment_id: string, lang: string) {
        let uri = this._uri + '/' + shipment_id + '/export-excel';
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

export default ShipmentsApi;