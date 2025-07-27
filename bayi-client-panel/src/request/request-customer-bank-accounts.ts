import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";

class CustomerBankAccountApi {
    private api;
    private _uri;

    constructor(company_id?: string) {
        this.api = useApi();
        if (company_id)
            this._uri = API_URIS.CUSTOMERS + `/${company_id}/bank_accounts`;
    }

    setUri(company_id: string) {
        this._uri = API_URIS.CUSTOMERS + `/${company_id}/bank_accounts`;
    }

    setCompanyId(company_id: string) {
        this.setUri(company_id);
    }

    async list(searchQuery = '') {
        let uri = this._uri + (searchQuery !== '' ? '?q=' + searchQuery : '');
        return this.api.get(uri);
    }

    async get(bank_id: string) {
        let uri = `${this._uri}/${bank_id}/edit`;
        return this.api.get(uri);
    }

    async add(data: any) {
        if (!this._uri) throw new Error('Company id is not set');
        return this.api.post(this._uri, data);
    }

    async update(data: any, bank_id: string) {
        let uri = `${this._uri}/${bank_id}`;
        return this.api.put(uri, data);
    }

    async remove(bank_id: string) {
        let uri = `${this._uri}/${bank_id}`;
        return this.api.delete(uri);
    }
}

export default CustomerBankAccountApi;