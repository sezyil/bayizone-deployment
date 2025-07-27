import API_URIS from '/@src/utils/api/api_uris';
import { IBaseResponse, useApi } from "/@src/composables/useApi";
import { IVariant } from '../shared/form/interface-variant';
import { IVariantValue } from '../shared/form/interface-variant-value';

export interface IVariantListItem extends IVariant {
    type_label: string;
    values?: IVariantValue[]
}

export default class VariantsApi {
    private api;
    private _uri;
    constructor() {
        this.api = useApi();
        this._uri = API_URIS.VARIANTS;
    }

    async list(params?: any) {
        return this.api.get(this._uri, {
            params: params
        });
    }

    async listWithValues() {
        return this.api.get<IBaseResponse<IVariantListItem[]>>(this._uri, {
            params: {
                withValues: true
            }
        });
    }

    async get(id: string, params?: any) {
        return this.api.get(this._uri + '/' + id, {
            params: params
        });
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