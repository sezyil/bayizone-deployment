import { CurrencyTypes } from "../response/interface-utils-response";
export type TransactionType = 'OFFER' | 'ORDER' | 'INVOICE' | 'RECEIPT' | 'WAYBILL' | 'RETURN';

export type TransactionIOType = 0 | 1


export interface ITransactionForm {
    company_customer_id: string;
    customer_order_id: string | null;
    currency: CurrencyTypes;
    fiche_no: string;
    fiche_type: TransactionType | '';
    date: string;
    description: string;
    io_type: TransactionIOType | '';
    due_date: string;
    amount: number;
    is_paid: boolean;
}



export interface ITransactionFormErrors {
    company_customer_id: string[];
    customer_order_id: string[];
    currency: string[];
    fiche_no: string[];
    fiche_type: string[];
    date: string[];
    description: string[];
    io_type: string[];
    due_date: string[];
    amount: string[];
    is_paid: string[];
}