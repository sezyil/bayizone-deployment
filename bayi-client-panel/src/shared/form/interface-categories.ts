import { AvailableLanguages } from "../common/common-language"
export interface ICategoriesForm {
    name: {
        [key in AvailableLanguages]: string;
    },
    parent_id: number,
}