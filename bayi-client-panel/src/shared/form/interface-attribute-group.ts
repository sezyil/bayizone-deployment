import { AvailableLanguages } from "../common/common-language"

interface InterfaceAttributeGroupForm {
    name: {
        [key in AvailableLanguages]: string;
    },
}

interface InterfaceAttributeGroupFormErrors {
    name: {
        [key in AvailableLanguages]: string[]
    },
}

export {
    InterfaceAttributeGroupForm,
    InterfaceAttributeGroupFormErrors
}