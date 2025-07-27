import { CurrencyTypes } from "../shared/response/interface-utils-response";

const getCurrencySymbol = (currency: CurrencyTypes | undefined): string | null => {
    switch (currency) {
        case 'tl':
            return '₺';
        case 'usd':
            return '$';
        case 'euro':
            return '€';
        case 'gbp':
            return '£';
        default:
            return null;
    }
}

const getCurrencyName = (currency: CurrencyTypes | undefined): string | null => {
    switch (currency) {
        case 'tl':
            return 'Türk Lirası';
        case 'usd':
            return 'Dolar';
        case 'euro':
            return 'Euro';
        case 'gbp':
            return 'Sterlin';
        default:
            return null;
    }
}

export {
    getCurrencySymbol,
    getCurrencyName
}