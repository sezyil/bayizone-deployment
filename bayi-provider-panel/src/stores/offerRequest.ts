import { ref } from 'vue'
import { acceptHMRUpdate, defineStore } from 'pinia'
import { IOfferCartProduct, IOfferProductCard, OfferRequestApi, OfferRequestProductApi } from '../request/request-offer-request'
const apiClass = new OfferRequestProductApi();
const offerRequestApi = new OfferRequestApi();
type TPageStep = "product" | "checkout" | "success"
export const useOfferRequest = defineStore('offerRequest', () => {
  const limit = 12
  const page = ref(1)
  const totalItem = ref(0)
  const productList = ref<IOfferProductCard[]>([])
  const selectedProductList = ref<IOfferCartProduct[]>([])
  const step = ref<TPageStep>("product")

  const setStep = (_step: TPageStep) => {
    step.value = _step
  }

  const getProducts = async () => {
    const { data } = await apiClass.list(undefined, limit, page.value);
    productList.value = data.data.items as IOfferProductCard[];
    totalItem.value = data.data.total;
  }

  const paginationData = computed(() => ({
    limit: limit,
    page: page.value,
    total: totalItem.value
  }))

  const addProduct = (product: IOfferCartProduct) => {
    selectedProductList.value.push(product)
  }

  const removeProduct = (index: number) => {
    selectedProductList.value.splice(index, 1)
  }

  const listOfSelectedProduct = computed(() => {
    return selectedProductList.value.map((item) => item.id)
  })

  const setPage = (_page: number) => {
    page.value = _page
  }

  const sendForm = async (data: any) => {
    return await offerRequestApi.add(data)
  }

  watch(page, () => {
    getProducts()
  })

  const $reset = () => {
    page.value = 1
    totalItem.value = 0
    productList.value = []
    selectedProductList.value = []
    step.value = "product"
  }


  return {
    totalItem,
    limit,
    page,
    step,
    setStep,
    setPage,
    selectedProductList,
    productList,
    listOfSelectedProduct,
    paginationData,
    addProduct,
    removeProduct,
    sendForm,
    getProducts,
    $reset
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
  import.meta.hot.accept(acceptHMRUpdate(useOfferRequest, import.meta.hot))
}
