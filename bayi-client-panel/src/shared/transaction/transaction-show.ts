export interface ITransactionShow {
    data: Data
}

interface Data {
    detail: Detail
    customer: Customer
    histories: History[]
    order: Order | null
}

export interface Detail {
    id: string
    fiche_no: string
    fiche_type: string
    fiche_type_label: string
    date: string
    description: any
    is_paid: boolean
    io_type: boolean
    amount: number
    formatted_amount: string
    due_date: any
    payment_closed_date: any
    created_at: string
    updated_at: string
}
interface Customer {
    id: string
    name: string
}
interface History {
    id: number
    description: string
    amount: number
    created_at: string
    payment_date: string
    payment_type: string
    payment_type_label: string
}

interface Order {
    id: string
    order_no: string
}
