import { AvailableLanguages, LanguageKeyBasedType } from "../common/common-language";

interface IVariant {
    id: string;
    type: IVariantType;
    is_active: boolean;
    is_default: boolean;
    name: string;
}

type IVariantType = 'COLOR' | 'DIMENSION'

const getVariantTypeDescription = (type: string): string => {
    switch (type) {
        case 'COLOR':
            return 'Renk';
        case 'DIMENSION':
            return 'Boyut';
        default:
            return 'Bilinmeyen';
    }
}

const getAllVariantTypes = (): { key: IVariantType, value: string }[] => {
    return [
        { key: 'COLOR', value: getVariantTypeDescription('COLOR') },
        { key: 'DIMENSION', value: getVariantTypeDescription('DIMENSION') }
    ]
}

interface IVariantForm extends Omit<IVariant, 'id' | 'name'> {
    names: LanguageKeyBasedType<string>;
}


type IVariantFormType = keyof IVariantForm;
interface IVariantFormErrors {
    type: string[]
    is_active: string[]
    is_default: string[]
    names: LanguageKeyBasedType<string[]>
}

export {
    IVariant,
    IVariantType,
    IVariantForm,
    IVariantFormType,
    IVariantFormErrors,
    getVariantTypeDescription,
    getAllVariantTypes
}