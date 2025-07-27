import API_URIS from "../utils/api/api_uris";
import { useApi } from "/@src/composables/useApi";
const api = useApi();
const uri = API_URIS.PROFILE_USER;

export const get = async () => {
    return await api.get(uri);
}

export const update = async (data: any) => {
    return await api.put(uri, data);
}
