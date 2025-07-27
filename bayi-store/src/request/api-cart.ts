import { IProductVariantColorData, IProductVariantDimensionData } from '../shared/product';
import { useApi } from '/@src/composables/useApi';
export interface IStoreProductFilter {
    search: string
    page: number
    limit: number
}

export interface IApiCartAddToCart {
    product_id: string
    quantity: number
    color: {
        id: string
        value: string
    }[]
    dimension: {
        id: string
        value: string
    }[]
}


class ApiCart {
    private api;
    public _uri: string;
    public customerId: string;

    constructor(customerId: string, uri: string) {
        this.api = useApi;
        this.customerId = customerId;
        this._uri = uri + '/cart';
    }

    async getCart() {
        return await this.api().get(this._uri);
    }

    async addToCart(request: IApiCartAddToCart) {
        let uri = this._uri + '/:product_id'.replace(':product_id', request.product_id);
        return await this.api().post(uri, {
            quantity: request.quantity,
            color: request.color,
            dimension: request.dimension
        });
    }

    async updateCart(request: IApiCartAddToCart) {
        let uri = this._uri + '/:product_id'.replace(':product_id', request.product_id);
        return await this.api().put(uri, {
            quantity: request.quantity
        });
    }

    async removeFromCart(productId: string) {
        let uri = this._uri + '/:product_id'.replace(':product_id', productId);
        return await this.api().delete(uri);
    }

    async clearCart() {
        return await this.api().delete(this._uri + '/clear');
    }
}

export default ApiCart;