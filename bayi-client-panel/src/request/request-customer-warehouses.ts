import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";

class CustomerWarehouseApi {
    private api;
    private _uri: string = '';

    constructor(company_id?: string) {
        this.api = useApi();
        if (company_id)
            this.setCompanyId(company_id);
    }

    setUri(company_id: string) {
        this._uri = API_URIS.CUSTOMERS + `/${company_id}/warehouses`;
    }

    setCompanyId(company_id: string) {
        this.setUri(company_id);
    }

    async list(searchQuery = '') {
        let uri = this._uri + (searchQuery !== '' ? '?q=' + searchQuery : '');
        return await this.api.get(uri);
    }

    async get(bank_id: string) {
        let uri = `${this._uri}/${bank_id}/edit`;
        return await this.api.get(uri);
    }

    async add(data: any) {
        if (!this._uri) throw new Error('Company id is not set');
        return await this.api.post(this._uri, data);
    }

    async update(data: any, bank_id: string) {
        let uri = `${this._uri}/${bank_id}`;
        return await this.api.put(uri, data);
    }

    async remove(bank_id: string) {
        let uri = `${this._uri}/${bank_id}`;
        return await this.api.delete(uri);
    }
}

export default CustomerWarehouseApi;