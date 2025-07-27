import { LanguageKeyBasedType } from "../common/common-language";
import { getVariantTypeDescription } from "./interface-variant";

interface IVariantValue {
    id: string;
    //only color
    variant_type: IVariantValueType;
    is_active: boolean;
    is_default: boolean;
    name: string;
}

type IVariantValueType = 'COLOR'


const getAllVariantValueTypes = (): { key: IVariantValueType, value: string }[] => {
    return [
        { key: 'COLOR', value: getVariantTypeDescription('COLOR') },
    ]
}

interface IVariantValueForm extends Omit<IVariantValue, 'id' | 'name'> {
    names: LanguageKeyBasedType<string>;
}


type IVariantValueFormType = keyof IVariantValueForm;
interface IVariantValueFormErrors {
    variant_type: string[]
    is_active: string[]
    is_default: string[]
    names: LanguageKeyBasedType<string[]>
}

export {
    IVariantValue,
    IVariantValueType,
    IVariantValueForm,
    IVariantValueFormType,
    IVariantValueFormErrors,
    getVariantTypeDescription,
    getAllVariantValueTypes
}