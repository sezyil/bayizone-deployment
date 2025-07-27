type AvailableLanguages = 'tr' | 'en';
interface ILanguage {
    value: AvailableLanguages;
    description: string;
    image: string;
    flag: string;
}

export type LanguageKeyBasedType<T> = { [key in AvailableLanguages]: T }

const LanguageGetter = (language: AvailableLanguages): ILanguage => {
    const flagGenerator = (language: AvailableLanguages) => `/images/flags/${language}_flag.svg`
    switch (language) {
        case 'tr':
            return { value: 'tr', description: 'Türkçe', image: 'tr', flag: flagGenerator('tr') };
        case 'en':
            return { value: 'en', description: 'English', image: 'en', flag: flagGenerator('en') };
        default:
            return { value: 'tr', description: 'Türkçe', image: 'tr', flag: flagGenerator('tr') };
    }
};



export {
    LanguageGetter,
    AvailableLanguages,
    ILanguage,
}