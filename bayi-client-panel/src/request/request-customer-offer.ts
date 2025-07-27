import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";
import { EnumOfferStatus } from '../shared/form/interface-offer';

class CustomerOfferApi {
    private api;
    private _uri;

    constructor(company_id?: string) {
        this.api = useApi();
        if (company_id)
            this._uri = API_URIS.CUSTOMERS + `/${company_id}/offers`;
    }

    setUri(company_id: string) {
        this._uri = API_URIS.CUSTOMERS + `/${company_id}/offers`;
    }

    setCompanyId(company_id: string) {
        this.setUri(company_id);
    }

    async list(searchQuery = '') {
        let uri = this._uri + (searchQuery !== '' ? '?q=' + searchQuery : '');
        return this.api.get(uri);
    }

    async get(offer_id: string) {
        let uri = API_URIS.CUSTOMERS + '/offers/' + offer_id + '/edit';
        return this.api.get(uri);
    }

    async add(data: any, request_id?: string) {
        if (!this._uri) throw new Error('Company id is not set');
        let _uri = this._uri;
        if (request_id) _uri += `?request_id=${request_id}`;
        return this.api.post(_uri, data);
    }

    async update(data: any, offer_id: string) {
        let uri = `${this._uri}/${offer_id}`;
        return this.api.put(uri, data);
    }

    async updateStatus(status: EnumOfferStatus, offer_id: string, resend: boolean = false) {
        let uri = `${this._uri}/${offer_id}/status`;
        let data: any = { status }
        if (resend) data['resend'] = resend;
        return this.api.patch(uri, data);
    }

    async preview(offer_id: string, lang?: string) {
        let uri = API_URIS.CUSTOMERS + '/offers/' + offer_id;
        return this.api.get(uri, { params: { lang: lang } });
    }

    async sendWhatsappNotification(offer_id: string) {
        let uri = `${this._uri}/${offer_id}/send-whatsapp-notification`;
        return this.api.post(uri);
    }

    async remove(offer_id: string) {
        let uri = `${this._uri}/${offer_id}`;
        return this.api.delete(uri);
    }

    public convertToOrder = async (offer_id: string) => this.api.post(`${this._uri}/${offer_id}/convert-to-order`);

    async downloadExcel(offer_id: string, lang: string) {
        let uri = API_URIS.CUSTOMERS + '/offers/' + offer_id + '/export-excel';
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

export default CustomerOfferApi;