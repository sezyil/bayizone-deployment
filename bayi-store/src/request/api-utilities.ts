import { useApi } from "../composables/useApi";
import API_URIS from "../utils/api/api_uris";


class ApiUtilities {
    private api;
    public _uri: string;
    constructor() {
        this.api = useApi;
        this._uri = API_URIS.ROOT + API_URIS.UTILS;
    }
    async requestCountry() { return this.api().get(this._uri + `/countries`); }
    async requestState(countryId: number) { return this.api().get(this._uri + `/country/${countryId}`); }
    async requestCity(districtId: number) { return this.api().get(this._uri + `/states/${districtId}`); }
    async requestDistrict(provinceId: number) { return this.api().get(this._uri + `/district/${provinceId}`); }
    async requestProvince() { return this.api().get(this._uri + `/provinces`); }
    async requestCurrency() { return this.api().get(this._uri + `/currencies`); }
}

export default ApiUtilities;