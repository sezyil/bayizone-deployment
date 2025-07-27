import { useApi } from "../composables/useApi";
import API_URIS from "../utils/api/api_uris";

export interface IServiceResponseRoot {
    data: IData
}

export interface IData {
    subscription: ISubscription
    has_active_subscription: boolean
    available_addons: IAvailableAddon[]
}

export interface ISubscription {
    id: number
    starts_at: string
    ends_at: string
    is_active: boolean
    user_count: number
    left_user_count: number
    sales_management: boolean
    simple_accounting: boolean
    online_store: boolean
    product_count: number
    left_product_count: number
    provider_panel_count: number
    left_provider_panel_count: number
    left_month: number
}

export interface IAvailableAddon {
    id: string
    type: string
    name: string
    is_boolean: number
    is_bulk: boolean
    bulk_quantity: number
    quantity: number
    price: number
    monthly_price: number
    last_price: number
}


const api = useApi()
const _uri = API_URIS.PROFILE_COMPANY;

const get = async () => api.get(_uri);

const update = async (data: any) => api.put(_uri, data);

const updateImage = async (data: any) => api.postForm(`${_uri}/image`, data);

const companyGetServices = async () => api.get(_uri + '/services');

const companyUpdateServices = async (data: any) => api.post(_uri + '/services', {
    addons: data
});



export {
    get,
    update,
    updateImage,
    companyGetServices,
    companyUpdateServices
}