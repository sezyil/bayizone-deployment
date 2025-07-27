import { IProductVariantColorData, IProductVariantDimensionData } from '../shared/product';
import { IStoreCustomerInfo } from '../stores/shopStore';
import ApiCart from './api-cart';
import { useApi } from '/@src/composables/useApi';
import API_URIS from '/@src/utils/api/api_uris';

export interface IStoreOfferFormProduct {
    id: string
    uuid: string
    quantity: number
    color: IStoreOfferFormProductVariant[] | null
    dimension: IStoreOfferFormProductVariant[] | null
}
export interface IStoreProductFilter {
    search: string
    page: number
    limit: number
    category: number | string
}

export interface IStoreOfferFormProductVariant {
    product_variant_id: string
    product_variant_value_id: string
    variant_id: string
}

export type ProductVariantType = '0' | 'COLOR' | 'DIMENSION'

export interface IStoreCreateOfferForm {
    customer: IStoreCustomerInfo,
    cart: IStoreOfferFormProduct[] | []
}

export interface ICategoryItem {
    id: number
    parent_id: number
    name: string
    is_default: boolean
    product_count: number
}


class ApiStore {
    private api;
    public _uri: string;
    public customerId: string;
    public CartApi: ApiCart;

    constructor(customerId: string) {
        this.api = useApi;
        this.customerId = customerId;
        this._uri = this.generateStoreUri();
        this.CartApi = new ApiCart(customerId, this._uri);
    }

    public get cart() {
        return this.CartApi;
    }

    private generateStoreUri() {
        let uri = '/:customer_id';
        return uri.replace(':customer_id', this.customerId);
    }

    async getStoreDetails() {
        let uri = this._uri + API_URIS.DETAIL;
        return await this.api().get(uri);
    }

    //get products
    async getProducts(filter: IStoreProductFilter) {
        let uri = this._uri + API_URIS.PRODUCTS;
        let _params = { ...filter, withValues: true }
        return await this.api().get(uri, { params: _params });
    }

    //get product
    async getProduct(productId: string) {
        let uri = this._uri + API_URIS.PRODUCT.replace(':product_id', productId);
        return await this.api().get(uri);
    }

    async createOffer(data: IStoreCreateOfferForm) {
        let uri = this._uri + API_URIS.OFFER;
        return await this.api().post(uri, data);
    }

    async getCategories(query?: string) {
        let uri = this._uri + API_URIS.CATEGORIES;
        return await this.api().get(uri, { params: { search: query } });
    }
}

export default ApiStore;