export interface IBankAccountListResponse {
    id: string
    bank_name: string
    branch_name: string
    account_name: string
    account_number: string
    iban: string
    swift_code: string
    currency: string
    currency_name: string
    status: boolean
}