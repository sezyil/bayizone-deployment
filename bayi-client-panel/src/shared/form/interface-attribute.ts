import type { AvailableLanguages } from "../common/common-language"

export interface IAttributeForm {
    name: {
        [key in AvailableLanguages]: string;
    }
    attribute_group_id: number,
}

export interface IAttributeFormErrors {
    name: {
        [key in AvailableLanguages]: string[]
    }
    attribute_group_id: string[]
}
