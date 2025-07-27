import { LanguageKeyBasedType } from "../common/common-language";

export interface IProductUnitForm {
    name: LanguageKeyBasedType<string>;
    short_name: string,
    is_active: number,
}
export interface IProductUnitFormErrors {
    name: LanguageKeyBasedType<string[]>;
    short_name: string[],
    is_active: string[],
}