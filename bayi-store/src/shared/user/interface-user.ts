interface IUser {
    id: string
    fullname: string
    email: string
    role: string
    customer_id: string
    language: string
    image: string | null
    hasCompanyInfo: boolean
}

interface IUserSubscription {
    ends_at: string
    is_active: boolean
    user_count: number
    left_user_count: number
    catalog_management: boolean
    proforma_invoices: boolean
    create_proposal: boolean
    proposal_management: boolean
    sales_management: boolean
    whatsapp_integration: boolean
    send_proposal_with_link: boolean
    simple_accounting: boolean
    plan_name?: string
}

export {
    IUser,
    IUserSubscription,
}