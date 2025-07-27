import API_URIS from '/@src/utils/api/api_uris';
import { useApi } from "/@src/composables/useApi";
export type DashboardAPITypes = 'cards' | 'order_summary' | 'offer_summary' | 'last_offers_orders' | 'last_transactions' | 'pie_chart'
export interface DashboardFilter {
    startDate?: string;
    endDate?: string;
}
class DashboardRequestApi {
    private api;
    private _uri: string = '';

    constructor() {
        this.api = useApi();
        this._uri = API_URIS.DASHBOARD;
    }

    async get(type: DashboardAPITypes, params?: DashboardFilter): Promise<any> {
        let uri = this._uri;
        switch (type) {
            case 'cards':
                uri += '/cards';
                break;
            case 'last_offers_orders':
                uri += '/last_offers_orders';
                break;
            case 'last_transactions':
                uri += '/last_transactions';
                break;
            case 'offer_summary':
                uri += '/offer_summary';
                break;
            case 'order_summary':
                uri += '/order_summary';
                break;
            case 'pie_chart':
                uri += '/pie_chart';
                break;
        }
        return await this.api.get(uri, {
            params: params
        });
    }
}

export default DashboardRequestApi;