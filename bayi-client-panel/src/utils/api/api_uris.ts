const {
    VITE_API_URL: VITE_API_URL,
} = import.meta.env;
const rootUri = VITE_API_URL;
const API_URIS = {
    //without client pfx
    ROOT: rootUri,
    //base url
    BASE_URL: rootUri + "/client",
    //auth root
    AUTH_ROOT: '/auth',
    //auth-login
    lOGIN: '/auth/login',
    //auth-register
    REGISTER: '/auth/register',
    //users
    USER: '/users',
    //attribute group
    ATTRIBUTE_GROUP: '/attribute_groups',
    //attributes
    ATTRIBUTE: '/attributes',
    //categories
    CATEGORY: '/categories',
    //products
    PRODUCT: '/products',
    //company
    PROFILE_COMPANY: '/profile/company',
    //profile
    PROFILE_USER: '/profile/user',
    //utilities
    UTILS: '/utilities',
    //customer
    CUSTOMERS: '/company_customers',
    //bank accounts
    BANK_ACCOUNTS: '/bank_accounts',
    //warehouses
    WAREHOUSES: '/warehouses',
    //product_units
    PRODUCT_UNITS: '/product_units',
    //permissions
    PERMISSIONS: '/permissions',
    //offer_requests
    OFFER_REQUESTS: '/offer_requests',
    //transactions
    TRANSACTIONS: '/transactions',
    //batch_processes
    BATCH_PROCESSES: '/batch_processes',
    //plans
    PLANS: '/plans',
    //customer_orders
    CUSTOMER_ORDERS: '/customer_orders',
    //payments
    PAYMENTS: '/payments',
    //dashboard
    DASHBOARD: '/dashboard',
    //variant
    VARIANTS: '/variants',
    //variant values
    VARIANT_VALUES: '/variant_values',
    //shipment
    SHIPMENTS: '/shipments',
}

export default API_URIS;