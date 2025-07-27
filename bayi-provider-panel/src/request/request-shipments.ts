import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";
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

    async get(shipment_id: string) {
        let uri = this._uri + '/' + shipment_id;
        return this.api.get(uri);
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