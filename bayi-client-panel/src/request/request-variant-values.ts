import API_URIS from '/@src/utils/api/api_uris';
import { IBaseResponse, useApi } from "/@src/composables/useApi";
import { IVariantValue } from '../shared/form/interface-variant-value';



export default class VariantValuesApi {
    private api;
    private _uri;
    constructor() {
        this.api = useApi();
        this._uri = API_URIS.VARIANT_VALUES;
    }

    async list(params?: any) {
        return this.api.get<IBaseResponse<IVariantValue[]>>(this._uri, {
            params: params
        });
    }

    async get(id: string) {
        return this.api.get(this._uri + '/' + id);
    }

    async add(data: any) {
        return this.api.post(this._uri, data);
    }

    async update(id: string, data: any) {
        let uri = this._uri + '/' + id;
        return this.api.put(uri, data)
    }

    async remove(id: string) {
        let uri = this._uri + '/' + id;
        return this.api.delete(uri)
    }
}