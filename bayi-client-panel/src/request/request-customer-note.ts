import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";

class CustomerNoteApi {
    private api;
    private _uri;

    constructor(company_id?: string) {
        this.api = useApi();
        if (company_id)
            this._uri = API_URIS.CUSTOMERS + `/${company_id}/notes`;
    }

    setUri(company_id: string) {
        this._uri = API_URIS.CUSTOMERS + `/${company_id}/notes`;
    }

    setCompanyId(company_id: string) {
        this.setUri(company_id);
    }

    async list(searchQuery = '') {
        let uri = this._uri + (searchQuery !== '' ? '?q=' + searchQuery : '');
        return this.api.get(uri);
    }

    async get(id: string) {
        let uri = `${this._uri}/${id}/edit`;
        return this.api.get(uri);
    }

    async add(data: any) {
        if (!this._uri) throw new Error('Company id is not set');
        return this.api.post(this._uri, data);
    }

    async update(data: any, id: string) {
        let uri = `${this._uri}/${id}`;
        return this.api.put(uri, data);
    }

    async remove(id: string) {
        let uri = `${this._uri}/${id}`;
        return this.api.delete(uri);
    }
}

export default CustomerNoteApi;