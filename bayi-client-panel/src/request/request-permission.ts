import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";
const api = useApi()
const _uri = API_URIS.PERMISSIONS;

class PermissionApi {
    static async list(searchQuery = '') {
        let uri = _uri + (searchQuery != '' ? '?q=' + searchQuery : '');
        return api.get(uri);
    }
    static async get(id: string) {
        let uri = `${_uri}/${id}/edit`;
        return api.get(uri);
    }
    static async update(_data: any, id: string) {
        let uri = `${_uri}/${id}`;
        return api.put(uri, {
            data: {
                id: id,
                items: _data
            }
        })
    }
}

export default PermissionApi;