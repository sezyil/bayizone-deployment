import { IProductVariantColorData, IProductVariantDimensionData } from "./product"

export interface IStoreCartData {
    id?: string
    uuid: string
    name: string
    images: Image[]
    view_count: number
    quantity: number
    colors: IProductVariantColorData[]
    selected_color: IStoreCartSelectedVariants[] | null
    dimensions: IProductVariantDimensionData[]
    selected_dimension: IStoreCartSelectedVariants[] | null
}


export interface IStoreCartProductDetail {
    length: string
    width: string
    height: string
    weight: string
    volume: string
    package: string
}


interface Image {
    itemImageSrc: string
}

export interface IStoreCartSelectedVariants {
    id: number
    type: string
    product_variant_id: string
    product_variant_value_id: string
    variant_id: string
    variant_value_id: string
    variant: Variant
    variant_value: VariantValue
}

interface Variant {
    id: string
    name: string
    variant_id: string
}

interface VariantValue {
    id: string
    name: any
    variant_value_id: any
    value: Value
}

interface Value {
    width: string
    height: string
    length: string
}
