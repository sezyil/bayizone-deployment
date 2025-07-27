import { CurrencyTypes } from "../response/interface-utils-response";

export interface ICustomerBankAccount {
  bank_name: string;       // banka adı
  branch_name: string;     // şube adı
  account_name: string;    // hesap adı
  account_number?: string; // hesap numarası (isteğe bağlı olabilir)
  iban?: string;           // iban (isteğe bağlı olabilir)
  swift_code?: string;     // swift kodu (isteğe bağlı olabilir)
  currency: CurrencyTypes;        // para birimi
  status: boolean;         // durum
}

export interface ICustomerBankAccountErrors {
  bank_name: string[];
  branch_name: string[];
  account_name: string[];
  account_number: string[];
  iban: string[];
  swift_code: string[];
  currency: string[];
  status: string[];
}