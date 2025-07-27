import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";

class CustomerBankAccountApi {
    private api;
    private _uri;

    constructor() {
        this.api = useApi();
        this._uri = API_URIS.BANK_ACCOUNTS;
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