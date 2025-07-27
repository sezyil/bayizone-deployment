import { ProductVariantType } from "../request/api-store"
import { AvailableLanguages } from "./common/common-language"

export interface IStoreProduct {
    uuid: string
    name: string
    description: string
    quantity: number
    images: Image[]
    view_count: number
    detail: IStoreProductDetail
    variants: IStoreProductVariant[]
    colors: IProductVariantColorData[]
    dimensions: IProductVariantDimensionData[]
}

export interface IStoreProductDetail {
    length: string
    width: string
    height: string
    weight: string
    volume: string
    package: string
}
export type ProductLanguageKeyBasedType<T> = { [key in AvailableLanguages]: T }


interface Image {
    itemImageSrc: string
}

export interface IStoreProductVariant {
    id?: string
    type: ProductVariantType;
    value: ProductLanguageKeyBasedType<string>;
}

export interface IProductVariantColorData {
    id: string
    variant_id: string
    name: string
    value: IProductVariantColorValueData[]
}


export interface IProductVariantColorValueData {
    id: string
    variant_value_id: string
    name: string
    root_variant_value_id: string
}

export interface IProductFormVariantDimension {
    length: number
    width: number
    height: number
}

export interface IProductVariantDimensionData {
    id: string
    variant_id: string
    name: string
    value: {
        id: string
        value: IProductFormVariantDimension
    }[]
}