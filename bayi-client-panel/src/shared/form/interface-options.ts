import { AvailableLanguages } from "../common/common-language";

interface IOptionForm {
    name: {
        [key in AvailableLanguages]: string;
    }
    values: IOptionValue[];
}

interface IOptionFormErrors {
    name: {
        [key in AvailableLanguages]: string[]
    };
    values: string[];
}

interface IOptionValue {
    id: number;
    name: {
        [key in AvailableLanguages]: string;
    };
}


export {
    IOptionForm,
    IOptionFormErrors,
    IOptionValue,
};
