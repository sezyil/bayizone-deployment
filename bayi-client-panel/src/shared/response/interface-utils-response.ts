export interface IProvinceResponse {
    id: number
    name: string
}

export interface IDistrictResponse {
    id: number
    name: string
}

export type CurrencyTypes = 'tl' | 'usd' | 'euro' | 'gbp'

export interface ICurrencyResponse {
    id: number
    title: string
    code: CurrencyTypes
    symbol_right: number
    rate: string
}

export interface IProductUnitResponse {
    id: number
    name: string
    short_name: string
    is_active: number
}