import { ref } from 'vue'
import { acceptHMRUpdate, defineStore } from 'pinia'
import ApiStore from '../request/api-store'
import { IStoreCartData } from '../shared/cart'
import { IApiCartAddToCart } from '../request/api-cart'

type IStoreCustomerType = 'individual' | 'corporate'

export interface IStoreDetail {
    id: string
    name: string
    image: string | null
    qr_code: string | null
    ai_support: boolean
    ai_catalog_id: number | null
}

export interface IStoreCustomerInfo {
    type: IStoreCustomerType
    authorized_name: string
    phone: string
    email: string
    address: string
    company_name: string
    country_id: number
    state_id: number
    city_id: number
    note: string
    currency: 'tl' | 'usd' | 'euro' | 'gbp'
}



export interface IStoreCartFormErrors {
    customer: {
        authorized_name: string[],
        company_name: string[],
        address: string[],
        country_id: string[],
        state_id: string[],
        city_id: string[],
        email: string[],
        note: string[],
        type: string[],
        phone: string[]
        currency: string[]
    },
    cart: {
        uuid: string[],
        quantity: string[],
    }[]
}




interface IStoreToastData {
    active: boolean | null
    type: 'added' | 'removed' | 'cleared' | null
}


export const useShopStore = defineStore('shopStore', () => {
    const storeId = ref('')
    const modalActive = ref(false)
    const cartList = ref<IStoreCartData[]>([])
    const cartFormErrors = ref<IStoreCartFormErrors>({
        customer: {
            authorized_name: [],
            company_name: [],
            address: [],
            country_id: [],
            state_id: [],
            city_id: [],
            email: [],
            note: [],
            type: [],
            phone: [],
            currency: []
        },
        cart: []
    })
    const storeDetail = ref<IStoreDetail | null>(null)
    var storeApi: ApiStore;
    const customerInfo = ref<IStoreCustomerInfo>({
        type: 'individual',
        authorized_name: '',
        phone: '',
        email: '',
        address: '',
        company_name: '',
        country_id: 0,
        state_id: 0,
        city_id: 0,
        currency: 'tl',
        note: ''
    });
    const toastData = ref<IStoreToastData>({
        active: null,
        type: null,
    })

    const triggerToastData: {
        addProduct: () => void
        removeProduct: () => void
        clearCart: () => void
        reset: () => void
    } = {
        addProduct: () => (toastData.value = { active: true, type: 'added' }),
        removeProduct: () => (toastData.value = { active: true, type: 'removed' }),
        clearCart: () => (toastData.value = { active: true, type: 'cleared' }),
        reset: () => (toastData.value = { active: false, type: null })
    }

    function setModalActive(value: boolean) {
        modalActive.value = value
    }

    const setStore = async (store: IStoreDetail) => {
        storeDetail.value = store
        storeApi = new ApiStore(store.id)
        storeId.value = store.id
    }

    const resetFormErrors = () => {
        cartFormErrors.value = {
            customer: {
                authorized_name: [],
                company_name: [],
                address: [],
                country_id: [],
                state_id: [],
                city_id: [],
                email: [],
                note: [],
                currency: [],
                type: [],
                phone: []
            },
            cart: []
        }
    }

    const resetForm = () => {
        resetFormErrors()
        customerInfo.value = {
            type: 'individual',
            authorized_name: '',
            phone: '',
            email: '',
            address: '',
            company_name: '',
            country_id: 0,
            state_id: 0,
            city_id: 0,
            currency: 'tl',
            note: ''
        }
    }

    const setFormErrors = (errors: any) => {
        resetFormErrors()
        cartFormErrors.value = errors
    }

    const getCurrentCart = async () => {
        try {
            const cart = await storeApi.cart.getCart()
            cartList.value = cart.data.data;
        } catch (e) {
            throw new Error('Failed to get cart')
        }
    }


    const getStore = computed(() => storeDetail.value)


    const addItem = async (item: IApiCartAddToCart) => {
        try {
            await storeApi.cart.addToCart({
                product_id: item.product_id,
                quantity: item.quantity,
                color: item.color,
                dimension: item.dimension
            });
            getCurrentCart()
            triggerToastData.addProduct()
        }
        catch (e) {
            console.log(e)
            throw new Error('Failed to add item to cart')
        }
    }

    const changeItem = async (item: IApiCartAddToCart) => {
        try {
            const existingItem = cartList.value.find((cartItem) => cartItem.uuid === item.product_id)
            if (existingItem) {
                await storeApi.cart.updateCart({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    color: item.color,
                    dimension: item.dimension
                });
                existingItem.quantity = item.quantity
            }
        }
        catch (e) {
            throw new Error('Failed to update item quantity')
        }

    }

    const removeItem = async (item: IStoreCartData) => {
        const existingItem = cartList.value.find((cartItem) => cartItem.id === item.id)
        if (existingItem && item.id) {
            try {
                await storeApi.cart.removeFromCart(item.id)
                getCurrentCart()
                triggerToastData.removeProduct()
            } catch (e) {
                throw new Error('Failed to remove item from cart')
            }

        }

    }

    const getStoreApi = (): ApiStore => {
        if (!storeApi) {
            throw new Error('Store Api not initialized')
        }
        return storeApi;
    }

    const clearCart = async () => {
        try {
            await storeApi.cart.clearCart()
            cartList.value = []
            triggerToastData.clearCart()
        } catch (e) {
            throw new Error('Failed to clear cart')
        }
    }

    return {
        storeId,
        modalActive,
        toastData,
        cartList,
        customerInfo,
        getStore,
        cartFormErrors,
        setModalActive,
        changeItem,
        addItem,
        removeItem,
        setStore,
        clearCart,
        getCurrentCart,
        triggerToastData,
        getStoreApi,
        setFormErrors,
        resetForm
    } as const
})

/**
 * Pinia supports Hot Module replacement so you can edit your stores and
 * interact with them directly in your app without reloading the page.
 *
 * @see https://pinia.esm.dev/cookbook/hot-module-replacement.html
 * @see https://vitejs.dev/guide/api-hmr.html
 */
if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useShopStore, import.meta.hot))
}
