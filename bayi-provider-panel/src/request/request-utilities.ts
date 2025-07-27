import { useApi } from "../composables/useApi";
import API_URIS from "../utils/api/api_uris";

const api = useApi();
const uri = API_URIS.ROOT + API_URIS.UTILS;

const makeRequest = async (_uri: string) => {
    if (_uri.charAt(0) !== "/") _uri = "/" + _uri;
    let finalizedUri = uri + _uri;
    return await api.get(finalizedUri);
}

export const requestCountry = async () => await makeRequest(`/countries`);

export const requestState = async (countryId: number) => await makeRequest(`/country/${countryId}`);

export const requestCity = async (districtId: number) => await makeRequest(`/states/${districtId}`);

export const requestDistrict = async (provinceId: number) => await makeRequest(`/district/${provinceId}`);

export const requestProvince = async () => await makeRequest(`/provinces`);

export const requestCurrency = async () => await makeRequest(`/currencies`);


