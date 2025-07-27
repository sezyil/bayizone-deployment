export interface IAttributeDataResponse {
    id: number
    customer_id: string
    attribute_group_id: number
    is_default: number
    group_description: GroupDescription
    descriptions: Descriptions[]
}

interface GroupDescription {
    id: number
    attribute_group_id: number
    language: string
    name: string
}

interface Descriptions {
    id: number
    attribute_id: number
    language: string
    name: string
}
