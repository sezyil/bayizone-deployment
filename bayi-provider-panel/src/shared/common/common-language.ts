type AvailableLanguages = 'tr' | 'en';
interface ILanguage {
    value: AvailableLanguages;
    description: string;
    image: string;
}
const LanguageGetter = (language: AvailableLanguages): ILanguage => {
    const imageGenerator = (str: string) => `/images/flags/${str}_flag.svg`
    switch (language) {
        case 'tr':
            return { value: 'tr', description: 'Türkçe', image: imageGenerator('tr') };
        case 'en':
            return { value: 'en', description: 'English', image: imageGenerator('en') };
        default:
            return { value: 'tr', description: 'Türkçe', image: imageGenerator('tr') };
    }
};

const SelectableLanguages: ILanguage[] = [
    LanguageGetter('tr'),
    LanguageGetter('en'),
];



export {
    LanguageGetter,
    AvailableLanguages,
    ILanguage,
    SelectableLanguages,
}