import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";
import { IOfferProductDetail } from '../shared/offer-product/offer-product-detail';
import { AvailableLanguages } from '../shared/common/common-language';
export type ProductVariantType = '0' | 'COLOR' | 'DIMENSION'
export interface IOfferProductCard {
    id: string
    sku: any
    name: string
    model: string
    image: string
    quantity: number
    width: number
    height: number
    length: number
    weight: number
    package: number
    volume: number
    images: string[]
    colors: IProductVariantColorData[]
    dimensions: IProductVariantDimensionData[]
}

export type ProductLanguageKeyBasedType<T> = { [key in AvailableLanguages]: T }

export interface IProductFormVariantDimension {
    length: number
    width: number
    height: number
}


export interface IProductVariantDimensionValue {
    length: number
    width: number
    height: number
}

export interface IProductVariantDimensionData {
    id: string
    variant_id: string
    name: string
    value: {
        id: string
        value: IProductVariantDimensionValue
    }[]
}

export interface IProductVariantColorData {
    id: string
    name: string
    variant_id: string
    value: IProductVariantColorDataValue[]
}

export interface IProductVariantColorDataValue {
    id: string
    variant_value_id: string
    name: string
    root_variant_value_id: string
}


/* forms */

export interface IProductFormVariantDimensionData {
    id: string
    name: string
    variant_id: string
    value: IProductVariantDimensionValue[]
}


export interface ModalVariant {
    color: string
    dimension: string
}

export interface IOfferCartProduct {
    id: string | null
    quantity: number
    note: string,
    product: IOfferProductDetail | null
    color: IProductVariantColorData[] | null
    dimension: IProductVariantDimensionData[] | null
}

export interface ICartProductOptions {
    required: boolean
    name: string
    value: any
    values: {
        id: number
        name: string
    }[]
}

export class OfferRequestApi {
    private api;
    private _uri;

    constructor() {
        this.api = useApi();
        this._uri = API_URIS.OFFER_REQUESTS;
    }

    async list(searchQuery = '') {
        let uri = this._uri + (searchQuery !== '' ? '?q=' + searchQuery : '');
        return this.api.get(uri);
    }

    async show(id: string) {
        let uri = `${this._uri}/${id}`;
        return this.api.get(uri);
    }

    async get(id: string) {
        let uri = `${this._uri}/${id}/edit`;
        return this.api.get(uri);
    }

    async add(data: any) {
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

export class OfferRequestProductApi {
    private api;
    private _uri;

    constructor() {
        this.api = useApi();
        this._uri = API_URIS.OFFER_REQUEST_PRODUCTS;
    }

    async list(searchQuery = '', limit = 10, page = 1) {
        let params = {
            params: {
                q: searchQuery,
                limit: limit,
                page: page
            }
        }

        return this.api.get(this._uri, params);
    }

    async get(id: string) {
        let uri = `${this._uri}/${id}`;
        return this.api.get(uri);
    }
}

