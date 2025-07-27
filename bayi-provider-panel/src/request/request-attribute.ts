import API_URIS from "../utils/api/api_uris";
import { useApi } from "/@src/composables/useApi";

const api = useApi();
const _uri = API_URIS.ATTRIBUTE;
const list = async (searchQuery = "") => {
    let uri = _uri + (searchQuery != "" ? "?q=" + searchQuery : "");
    return api.get(uri);
};

const get = async (id: number) => {
    let uri = _uri + "/" + id + "/edit";
    return api.get(uri);
};

const add = async (data: any) => {
    return api.post(_uri, data);
};

const update = async (data: any, id: number) => {
    let uri = _uri + "/" + id;
    return api.put(uri, data);
};
const _delete = async (id: number) => {
    let uri = _uri + "/" + id;
    return api.delete(uri);
};

export { list, get, add, update, _delete };
