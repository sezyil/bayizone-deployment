import { CurrencyTypes } from "/@src/shared/response/interface-utils-response";

export interface IBankAccountForm {
    bank_name: string;
    branch_name: string;
    account_name: string;
    account_number: string;
    iban: string;
    swift_code: string;
    currency: CurrencyTypes; // bigint yerine number tipi kullanıyoruz
    status: boolean; // tinyint(1) yerine boolean tipi kullanıyoruz
}

export interface IBankAccountFormErrors {
    bank_name: string[];
    branch_name: string[];
    account_name: string[];
    account_number: string[];
    iban: string[];
    swift_code: string[];
    currency: string[];
    status: string[];
}