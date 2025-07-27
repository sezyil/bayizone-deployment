import { useApi } from "../composables/useApi";
import API_URIS from "../utils/api/api_uris";

const api = useApi()
const list = async (searchQuery = '') => {
    let uri = API_URIS.ATTRIBUTE_GROUP + (searchQuery != '' ? '?q=' + searchQuery : '');
    return api.get(uri);
}

const get = async (id?: number | string) => {
    let uri = API_URIS.ATTRIBUTE_GROUP + '/' + id + '/edit';
    return api.get(uri);
}

const add = async (data: any) => {
    return api.post(API_URIS.ATTRIBUTE_GROUP, data);
}

const update = async (data: any, id: number) => {
    let uri = API_URIS.ATTRIBUTE_GROUP + '/' + id;
    return api.put(uri, data)
}
const _delete = async (id: number) => {
    let uri = API_URIS.ATTRIBUTE_GROUP + '/' + id;
    return api.delete(uri)
}

export {
    list,
    get,
    add,
    update,
    _delete
}