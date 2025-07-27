import { IRequestPayDataResponse } from './../request/request-payment';
import { acceptHMRUpdate, defineStore } from 'pinia'

export interface IPaymentStoreInvoiceDetail {
  invoice_firm_name: string
  invoice_tax_no: string
  invoice_tax_administration: string
  invoice_address: string
  invoice_country_id: number
  invoice_country: string
  invoice_state_id: number
  invoice_state: string
  invoice_city_id: number
  invoice_city: string
  invoice_postcode: string
  invoice_email: string
  invoice_phone: any
}

export const usePaymentStore = defineStore('paymentStore', () => {
  const orderData = ref<IRequestPayDataResponse | undefined>(undefined)

  const setOrderData = (data: IRequestPayDataResponse) => {
    orderData.value = data
  }


  //$reset
  function $reset() {
    orderData.value = undefined
  }


  function setInvoice(invoice: IPaymentStoreInvoiceDetail) {
    if (orderData.value) {
      orderData.value.invoice_firm_name = invoice.invoice_firm_name
      orderData.value.invoice_tax_no = invoice.invoice_tax_no
      orderData.value.invoice_tax_administration = invoice.invoice_tax_administration
      orderData.value.invoice_address = invoice.invoice_address
      orderData.value.invoice_country_id = invoice.invoice_country_id
      orderData.value.invoice_country = invoice.invoice_country
      orderData.value.invoice_state_id = invoice.invoice_state_id
      orderData.value.invoice_state = invoice.invoice_state
      orderData.value.invoice_city_id = invoice.invoice_city_id
      orderData.value.invoice_city = invoice.invoice_city
      orderData.value.invoice_postcode = invoice.invoice_postcode
      orderData.value.invoice_email = invoice.invoice_email
      orderData.value.invoice_phone = invoice.invoice_phone
    }
  }

  const getInvoice = computed(() => {
    let invoice: IPaymentStoreInvoiceDetail = {
      invoice_firm_name: '',
      invoice_tax_no: '',
      invoice_tax_administration: '',
      invoice_address: '',
      invoice_country_id: 0,
      invoice_country: '',
      invoice_state_id: 0,
      invoice_state: '',
      invoice_city_id: 0,
      invoice_city: '',
      invoice_postcode: '',
      invoice_email: '',
      invoice_phone: ''
    }

    if (orderData.value) {
      invoice.invoice_firm_name = orderData.value.invoice_firm_name
      invoice.invoice_tax_no = orderData.value.invoice_tax_no
      invoice.invoice_tax_administration = orderData.value.invoice_tax_administration
      invoice.invoice_address = orderData.value.invoice_address
      invoice.invoice_country_id = orderData.value.invoice_country_id
      invoice.invoice_country = orderData.value.invoice_country
      invoice.invoice_state_id = orderData.value.invoice_state_id
      invoice.invoice_state = orderData.value.invoice_state
      invoice.invoice_city_id = orderData.value.invoice_city_id
      invoice.invoice_city = orderData.value.invoice_city
      invoice.invoice_postcode = orderData.value.invoice_postcode
      invoice.invoice_email = orderData.value.invoice_email
      invoice.invoice_phone = orderData.value.invoice_phone
    }

    return invoice
  });

  const setPaymentMethod = (paymentMethod: any) => {
    if (orderData.value) {
      orderData.value.payment_method = paymentMethod
    }
  }

  return {
    orderData,
    setOrderData,
    setInvoice,
    getInvoice,
    setPaymentMethod,
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
  import.meta.hot.accept(acceptHMRUpdate(usePaymentStore, import.meta.hot))
}
