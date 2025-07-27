export interface ICategoriesDataResponse {
    id: number
    customer_id: string
    parent_id: number
    is_default: number
    descriptions_lang: Descriptions[]
}

export interface Descriptions {
    id: number
    category_id: number
    language: string
    name: string
}
