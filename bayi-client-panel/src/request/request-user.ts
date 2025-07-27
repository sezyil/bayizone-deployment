import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";
const api = useApi()
const _uri = API_URIS.USER;
const list = async (searchQuery = '') => {
    let uri = _uri + (searchQuery != '' ? '?q=' + searchQuery : '');
    return api.get(uri);
}

const get = async (id: string) => {
    let uri = `${_uri}/${id}/edit`;
    return api.get(uri);
}

const add = async (data: any) => {
    return api.post(_uri, data);
}

const get_roles = async () => {
    return api.get(`${_uri}/create`);
}

const update = async (data: any, id: string) => {
    let uri = `${_uri}/${id}`;
    return api.put(uri, data)
}
const remove = async (id: string) => {
    let uri = `${_uri}/${id}`;
    return api.delete(uri)
}

export {
    list,
    get,
    get_roles,
    add,
    update,
    remove
}