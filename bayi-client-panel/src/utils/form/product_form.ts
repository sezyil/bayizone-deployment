import type { ECompanyCustomerGroup } from './../../shared/form/interface-company-customer';
import { IProduct, IProductErrors, IProductForm, IProductFormErrors } from "../../shared/product/interface-product";

export const serializeOptionTemplate = (data: any) => {
    let serialized = {
        "id": 0,
        "option_id": data.id,
        "required": 0,
        "type": data.type,
        "name": data.description.name,
        "values": [],
        "available": availableSerializer()
    }

    function availableSerializer() {
        return data.option_value.map((e: any) => ({
            id: e.id,
            option_id: e.option_id,
            name: e.description.name,
        }));
    }
    return serialized;
}

export const resetProductData = () => {
    const x: IProductForm = {
        name: '',
        names: {
            en: '',
            tr: ''
        },
        sku: '',
        descriptions: {
            en: '',
            tr: ''
        },
        model: '',
        upc: '',
        ean: '',
        mpn: '',
        quantity: 0,
        unit_id: 0,
        minimum: 1,
        length: 0,
        width: 0,
        height: 0,
        weight: 0,
        volume: 0,
        package: '',
        status: 1,
        description: '',
        price_tl: 0,
        price_usd: 0,
        price_euro: 0,
        price_gbp: 0,
        default_currency: 'tl',
        images: [],
        attributes: [],
        options: [],
        links: {
            categories: [],
        },
        store_visibility: false,
        active_customer_group: [
            'CUSTOMER',
            'POTENTIAL',
            'SUPPLIER'
        ],
        colors: [],
        dimensions: []
    }
    return x;
}

export const resetProductErrors = () => {
    const x: IProductFormErrors = {
        names: {
            en: [],
            tr: []
        },
        descriptions: {
            en: [],
            tr: []
        },
        model: [],
        sku: [],
        upc: [],
        ean: [],
        mpn: [],
        quantity: [],
        unit_id: [],
        minimum: [],
        length: [],
        width: [],
        height: [],
        weight: [],
        volume: [],
        package: [],
        price_tl: [],
        price_usd: [],
        price_euro: [],
        price_gbp: [],
        default_currency: [],
        status: [],
        images: [],
        attributes: [],
        active_customer_group: [],
        store_visibility: [],
        links: [],
        colors: [],
        dimensions: []
    }
    return x;
}

export const highlightErrorTabs = () => {
    //find in .tab-content all .tab-pane
    const erroredTabs = {} as any;
    const tabPanes = document.querySelectorAll('.tab-content .tab-pane');
    //loop through all .tab-pane
    tabPanes.forEach((tabPane: any) => {
        const el = tabPane as HTMLElement;
        const elementId = el.id.replace('pane_', '');
        //if tabPane child elements has .is-invalid class
        if (el.querySelectorAll('.invalid-feedback').length > 0) {
            //add tabPane border-danger class
            erroredTabs[elementId] = true;
        } else { //else remove tabPane border-danger class
            erroredTabs[elementId] = false;
        }
    });
    return erroredTabs;

}
