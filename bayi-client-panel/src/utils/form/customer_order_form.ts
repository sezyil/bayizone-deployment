
import { EnumCustomerOrderStatus, ICustomerOrderForm, ICustomerOrderFormDetail, ICustomerOrderFormErrors, ICustomerOrderFormDetailErrors, ICustomerOrderFormLine, ICustomerOrderFormLineErrors } from "/@src/shared/form/interface-customer-order"
const tmpDetailData: ICustomerOrderFormDetail = {
    company_customer_id: "",
    total_price: 0,
    total_tax: 0,
    total_discount: 0,
    grand_total: 0,
    currency: "tl",
    note: "",
    billing_address: "",
    billing_city_id: 0,
    billing_country_id: 0,
    billing_state_id: 0,
    is_international: false,
    status: EnumCustomerOrderStatus.APPROVED,
    order_date:'',
    order_no: '',
    delivery_type: '',
    payment_type: '',
    total_volume: 0,
    total_package: 0,
    incoterms: '',
}

const tmpOrderFormErrors: ICustomerOrderFormDetailErrors = {
    company_customer_id: [],
    currency: [],
    total_price: [],
    total_tax: [],
    total_discount: [],
    grand_total: [],
    billing_address: [],
    billing_city_id: [],
    billing_state_id: [],
    billing_country_id: [],
    note: [],
    status: [],
    is_international: [],
    order_date: [],
    order_no: [],
    delivery_type: [],
    payment_type: [],
    total_volume: [],
    total_package: [],
    incoterms: [],
}

export const resetOrderForm = (): ICustomerOrderForm => ({
    detail: { ...tmpDetailData },
    lines: []
})

export const resetOrderFormErrors = () => {
    let x = { ...tmpOrderFormErrors }
    return {
        detail: x,
        lines: []
    } as ICustomerOrderFormErrors
}
export const addOrderFormLine = (): ICustomerOrderFormLine => {
    return {
        product_id: 0,
        product_name: "",
        product_code: "",
        product_unit: "",
        product_image_url: "",
        unit_id: 0,
        quantity: 0,
        unit_price: 0,
        tax_rate: 0,
        unit_discount_price: 0,
        unit_discount_rate: 0,
        total_discount_price: 0,
        total_price: 0,
        grand_total: 0,
        total_package: 0,
        total_volume: 0,
        unit_package: 0,
        unit_volume: 0,
        note: "",
        color: null,
        dimension: null,
    }
}

export const addOrderFormLineError = (): ICustomerOrderFormLineErrors => ({
    product_id: [],
    product_name: [],
    product_code: [],
    product_unit: [],
    product_image_url: [],
    unit_id: [],
    quantity: [],
    unit_price: [],
    tax_rate: [],
    unit_discount_price: [],
    unit_discount_rate: [],
    total_discount_price: [],
    total_price: [],
    grand_total: [],
    note: [],
    unit_volume: [],
    color: [],
    dimension: [],
    total_volume: [],
    total_package: [],
    unit_package: [],
})
