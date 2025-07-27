import { ECompanyCustomerGroup } from './../form/interface-company-customer';
import { AvailableLanguages } from "../common/common-language";
import { CurrencyTypes } from "../response/interface-utils-response";
import { IProductAttribute } from "./interface-product-attribute";
import { IProductImage } from "./interface-product-image";
import { ILinks } from "./interface-product-links";
import { IProductOption } from "./interface-product-option";

interface IProduct {
    name: string;
    names: { [key in AvailableLanguages]: string };
    description: string;
    descriptions: { [key in AvailableLanguages]: string };
    model: string;
    sku: string;
    upc: string;
    ean: string;
    mpn: string;
    quantity: number;
    unit_id: number;
    minimum: number;
    length: number;
    width: number;
    height: number;
    weight: number;
    volume: number;
    package: string;
    price_tl: number
    price_usd: number
    price_euro: number
    price_gbp: number
    default_currency: CurrencyTypes
    status: number;
    images: IProductImage[];
    attributes: IProductAttribute[];
    colors: IProductVariantColorData[]
    dimensions: IProductVariantDimensionData[]
    options: IProductOption[];
    links: ILinks;
    store_visibility: boolean;
    active_customer_group: ECompanyCustomerGroup[];
}

interface IProductErrors {
    names: {
        [key in AvailableLanguages]: string[];
    }
    descriptions: {
        [key in AvailableLanguages]: string[];
    }
    model: string[];
    sku: string[];
    upc: string[];
    ean: string[];
    mpn: string[];
    quantity: string[];
    unit_id: string[];
    minimum: string[];
    length: string[];
    width: string[];
    height: string[];
    weight: string[];
    volume: string[];
    package: string[];
    price_tl: string[]
    price_usd: string[]
    price_euro: string[]
    price_gbp: string[]
    default_currency: string[]
    status: string[];
    images: string[] | {
        image: string[];
    }[];
    attributes: string[][];
    links: string[];
    store_visibility: string[];
    active_customer_group: string[];
}

export type ProductVariantType = '0' | 'COLOR' | 'DIMENSION'

export type ProductLanguageKeyBasedType<T> = { [key in AvailableLanguages]: T }

export interface IProductVariantDimensionValue {
    length: number
    width: number
    height: number
}

interface IProductVariant {
    product_id?: number;
    type: ProductVariantType;
    name: any;
    value: ProductLanguageKeyBasedType<string> | IProductVariantDimensionValue;
}

interface IProductList {
    id: number
    sku: string | null
    name: string
    model: string
    image: string
    quantity: number
    default_currency: CurrencyTypes
    default_price: number
    price_tl: number
    price_usd: number
    price_euro: number
    price_gbp: number
    status: boolean
    volume: number
    package: number
    variants: IProductVariant[];
    colors: IProductVariantColorData[]
    dimensions: IProductVariantDimensionData[]
}

export interface IProductVariantDimensionData {
    id: string
    variant_id: string
    value: {
        id: string
        value: IProductVariantDimensionValue
    }[]
}

export interface IProductVariantColorData {
    id: string
    variant_id: string
    value: IProductVariantColorDataValue[]
}

export interface IProductVariantColorDataValue {
    id: string
    variant_value_id: string
    name: string
    root_variant_value_id: string
}


/* forms */

export interface IProductFormVariantDimensionData {
    id: string
    variant_id: string
    value: IProductVariantDimensionValue[]
}

export interface IProductForm extends Omit<IProduct, 'colors' | 'dimensions'> {
    colors: IProductVariantFormColorData[]
    dimensions: IProductFormVariantDimensionData[]
}

export interface IProductVariantFormColorData {
    id: string
    variant_id: string
    variant_value_id: string[]
}


export interface IProductFormErrors extends IProductErrors {
    colors: {
        id: string[];
        variant_id: string[];
        variant_value_id: string[];
    }[]
    dimensions: {
        id: string[];
        variant_id: string[];
        value: {
            width: string[];
            height: string[];
            length: string[];
        }[]
    }[]
}


export {
    IProduct,
    IProductErrors,
    IProductVariant,
    IProductList
}