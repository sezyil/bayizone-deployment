const {
    VITE_API_URL: VITE_API_URL,
} = import.meta.env;
const rootUri = VITE_API_URL;
const API_URIS = {
    //without client pfx
    ROOT: rootUri,
    //base url
    BASE_URL: rootUri + "/dealer",
    //auth root
    AUTH_ROOT: '/auth',
    //auth-login
    lOGIN: '/auth/login',
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
    //bank accounts
    BANK_ACCOUNTS: '/bank_accounts',
    //warehouses
    WAREHOUSES: '/warehouses',
    //product_units
    PRODUCT_UNITS: '/product_units',
    //offers
    OFFERS: '/offers',
    //offer requests
    OFFER_REQUESTS: '/offer_requests',
    //offer request products
    OFFER_REQUEST_PRODUCTS: '/offer_request_products',
    //orders
    ORDERS: '/orders',
    SHIPMENTS: '/shipments',
}

export default API_URIS;