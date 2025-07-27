interface IProductCategory {
    id?: number;
    product_id?: number;
    category_id: number;
}

interface IProductCategoryErrors {
    id: string[]
    product_id: string[]
    name: string[]
    category_id: string[]
}

export {
    IProductCategory,
    IProductCategoryErrors
}