import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";

class WarehouseApi {
    private api;
    private _uri: string = '';

    constructor() {
        this.api = useApi();
        this._uri = API_URIS.WAREHOUSES;
    }

    async list(searchQuery = '') {
        let uri = this._uri + (searchQuery !== '' ? '?q=' + searchQuery : '');
        return await this.api.get(uri);
    }

    async get(id: string) {
        let uri = `${this._uri}/${id}/edit`;
        return await this.api.get(uri);
    }

    async add(data: any) {
        return await this.api.post(this._uri, data);
    }

    async update(data: any, id: string) {
        let uri = `${this._uri}/${id}`;
        return await this.api.put(uri, data);
    }

    async remove(id: string) {
        let uri = `${this._uri}/${id}`;
        return await this.api.delete(uri);
    }
}

export default WarehouseApi;