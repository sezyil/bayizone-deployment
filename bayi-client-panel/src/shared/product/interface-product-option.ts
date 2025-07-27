export interface IProductOption {
    id?: number
    option_id: number
    name: string
    required: boolean
    values: IProductOptionValue[]
    available: IProductOptionAvailable[]
}

export interface IProductOptionValue {
    id: number
    option_value_id: string
    add_to_price: number
    price: string
}


export interface IProductOptionAvailable {
    id: number
    option_id: number
    name: string
}