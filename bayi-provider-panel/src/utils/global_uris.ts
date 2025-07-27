const {
    VITE_ROOT_URL: ROOT_URL,
    VITE_STORE_URL: STORE_URL
} = import.meta.env;
export const GLOB_URIS = {
    BASE_URL: ROOT_URL + '/',
    NO_IMAGE: '/images/no_image.png',
    PRODUCT_IMAGE: "/images/products/",
    STOREURI: STORE_URL
}

export const get_product_image_url = (image_path: string) => {
    return GLOB_URIS.BASE_URL + image_path
}