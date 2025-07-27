import { useApi } from "../composables/useApi";
import { IProduct, IProductForm } from "../shared/product/interface-product";
import API_URIS from "../utils/api/api_uris";

const api = useApi();
const _uri = API_URIS.PRODUCT;
const list = async (searchQuery = '', query: object = {}) => {
    let uri = _uri + (searchQuery != '' ? '?q=' + searchQuery : '');
    return await api.get(uri, {
        params: query
    });
}

const get = async (id: number) => {
    let uri = _uri + '/' + id + '/edit';
    return await api.get(uri);
}

const add = async (data: IProductForm) => {
    return await api.post(_uri, data);
}

const update = async (data: IProductForm, id: number) => {
    let uri = _uri + '/' + id;
    return await api.put(uri, data)
}
const _delete = async (id: number) => {
    let uri = _uri + '/' + id;
    return await api.delete(uri)
}

const duplicateProduct = async (id: number) => {
    let uri = _uri + '/duplicate/' + id;
    return await api.post(uri);
}

const imageUpload = async (formData: FormData) => {
    return await api.postForm('/product_image', formData);
}

const syncWithAi = async (id: number) => {
    let uri = _uri + '/sync_ai/' + id;
    return await api.post(uri);
}

const productAutocomplete = async (searchQuery = '', withDefault: boolean = false) => {
    let query: any = { wd: withDefault }
    if (searchQuery != '') {
        query['q'] = searchQuery
    }

    let uri = _uri + '/autocomplete'
    return api.get(uri, {
        params: query
    });
}

export {
    list,
    get,
    add,
    update,
    _delete,
    duplicateProduct,
    imageUpload,
    syncWithAi,
    productAutocomplete
}