interface IUser {
    id: string
    fullname: string
    email: string
    role: string
    customer_id: string
    language: string
    image: string | null
    hasCompanyInfo: boolean
    is_super_user: boolean
    wizard_completed: boolean
    ai_support: boolean
}

interface IUserSubscription {
    ends_at: string
    is_active: boolean
    user_count: number
    is_trial: boolean
    trial_ends_at: string
    left_user_count: number
    sales_management: boolean
    simple_accounting: boolean
    online_store: boolean
    product_count: number
    left_product_count: number
    provider_panel_count: number
    left_provider_panel_count: number
    plan_name?: string

}

export {
    IUser,
    IUserSubscription,
}