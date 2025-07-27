import { useApi } from "../composables/useApi";
import API_URIS from "../utils/api/api_uris";

const api = useApi()
const _uri = API_URIS.PROFILE_COMPANY;

const get = async () => api.get(_uri);

const update = async (data: any) => api.put(_uri, data);

const updateImage = async (data: any) => api.postForm(`${_uri}/image`, data);

export {
    get,
    update,
    updateImage
}