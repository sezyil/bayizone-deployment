export interface IOfferProductDetail {
    name: string
    sku: any
    model: string
    upc: any
    ean: any
    mpn: any
    quantity: number
    unit_id: number
    minimum: number
    length: string
    width: string
    height: string
    weight: string
    status: number
    description: string
    price: string
    images: IOfferProductImage[]
    image?: string | null
    attributes: any[]
    options: IOfferProductOption[]
    links: IOfferProductLinks
}

export interface IOfferProductImage {
    id: number
    image: string
    sort_order: number
}


export interface IOfferProductOption {
    id: number
    option_id: number
    required: boolean
    name: string
    values: IOfferProductValue[]
}

export interface IOfferProductValue {
    id: number
    name: string
}

export interface IOfferProductLinks {
    categories: any[]
}