const {
    VITE_API_URL: VITE_API_URL,
} = import.meta.env;
const rootUri = VITE_API_URL;
const API_URIS = {
    //without client pfx
    ROOT: rootUri,
    DETAIL: '/details',
    //base url
    BASE_URL: rootUri + "/store",
    PRODUCT: '/product/:product_id',
    PRODUCTS: '/products',
    OFFER: '/offer',
    UTILS: '/utilities',
    CATEGORIES: '/categories',
}

export default API_URIS;