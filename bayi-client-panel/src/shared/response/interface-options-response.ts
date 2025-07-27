export interface IOptionsDataResponse {
  id: number
  is_default: number
  type: string
  option_value: OptionValue[]
  descriptions: Descriptions[]
}

export interface OptionValue {
  id: number
  option_id: number
  descriptions: OptValDescriptions[]
}

export interface OptValDescriptions {
  id: number
  option_value_id: number
  language: string
  name: string
}

export interface Descriptions {
  id: number
  option_id: number
  language: string
  name: string
}
