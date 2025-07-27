import { useApi } from "../composables/useApi";

const api = useApi()
const _uri = '/categories';
const list = async (searchQuery = '') => {
    let uri = _uri + (searchQuery != '' ? '?q=' + searchQuery : '');
    return api.get(uri);
}

const categoryAutocomplete = async (searchQuery = '', withDefault: boolean = false) => {
    let query: any = { wd: withDefault }
    if (searchQuery != '') {
        query['q'] = searchQuery
    }

    let uri = _uri + '/autocomplete'
    return api.get(uri, {
        params: query
    });
}

const get = async (id: number) => {
    let uri = _uri + '/' + id + '/edit';
    return api.get(uri);
}

const add = async (data: any) => {
    return api.post(_uri, data);
}

const update = async (data: any, id: number) => {
    let uri = _uri + '/' + id;
    return api.put(uri, data)
}
const remove = async (id: number) => {
    let uri = _uri + '/' + id;
    return api.delete(uri)
}

export {
    list,
    get,
    add,
    update,
    remove,
    categoryAutocomplete
}