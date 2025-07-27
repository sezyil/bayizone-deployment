import { TransactionType } from "../../shared/form/interface-transaction";
export const TransactionTypeDescription = (type: TransactionType | string) => {
    switch (type) {
        case 'ORDER':
            return "Sipariş";
        case 'INVOICE':
            return "Fatura";
        case 'RECEIPT':
            return "Tahsilat";
        case 'WAYBILL':
            return "Tediye";
        case 'RETURN':
            return "İade";
        default:
            return "Bilinmeyen";
    }
}

export enum EnumTransactionIO {
    /**
     * Borç
     */
    DEBT = 0,
    /**
     * Alacak
     */
    CREDIT = 1
}

export const TransactionIOTypeList = () => {
    return [
        { value: EnumTransactionIO.DEBT, label: 'Borç' },
        { value: EnumTransactionIO.CREDIT, label: 'Alacak' }
    ]
}

export const TransactionIOTypeDescription = (type: EnumTransactionIO) => {
    switch (type) {
        case EnumTransactionIO.DEBT:
            return "Borç";
        case EnumTransactionIO.CREDIT:
            return "Alacak";
        default:
            return "Bilinmeyen";
    }
}



export const TransactionTypesList = () => {
    return [
        { value: 'INVOICE', label: TransactionTypeDescription('INVOICE') },
        { value: 'RECEIPT', label: TransactionTypeDescription('RECEIPT') },
        { value: 'WAYBILL', label: TransactionTypeDescription('WAYBILL') },
        { value: 'RETURN', label: TransactionTypeDescription('RETURN') },
    ]
}

