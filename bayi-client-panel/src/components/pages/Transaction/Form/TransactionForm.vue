<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import { list as customerRequest } from '/@src/request/request-customer';
import TransactionApi, { ITransactionApiGetFormInputResponse } from '/@src/request/request-transaction';
import { ITransactionForm, TransactionIOType, type TransactionType } from '/@src/shared/form/interface-transaction';
import { useTransactionStore } from '/@src/stores/transactionStore';
import { TransactionTypesList } from '/@src/utils/form/transaction_form';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
import { getCurrencySymbol } from '/@src/utils/currency_helper';
import { CurrencyTypes } from '/@src/shared/response/interface-utils-response';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import InputSwitch from 'primevue/inputswitch';
const swal = useSwal()
const api = new TransactionApi();
const store = useTransactionStore()
const router = useRouter()
const route = useRoute()
const viewWrapper = useViewWrapper()
//route company_customer_id
const { company_customer_id: route_customer_id } = route.query as { company_customer_id: string }

const customers = ref<Array<{
    id: string
    name: string
}>>([])

const getCustomers = async () => {
    try {
        const { data } = await customerRequest()
        customers.value = data.data
    } catch (e) {
        swal.fire({
            icon: 'error',
            title: 'Hata',
            text: 'Müşteriler yüklenirken bir hata oluştu'
        })
    }
}
const transactionList = TransactionTypesList();
const orderBasedTransaction = ref<boolean>(false)
const availableOrders = ref<ITransactionApiGetFormInputResponse['data']['customer_orders'] | null>(null)
const hasCompanyCustomer = computed(() => route_customer_id ? true : false)
const paidSwitchActive = computed(() => store.transactionForm.fiche_type !== 'RECEIPT')


const saveTransaction = async () => {
    try {
        let postData = { ...store.transactionForm, order_based: orderBasedTransaction.value }
        await api.add(postData)
        swal.fire({
            icon: 'success',
            title: 'Başarılı',
            text: 'İşlem başarıyla kaydedildi'
        }).then(() => {
            if (route_customer_id) router.push({
                path: '/app/customers/:id'.replace(':id', route_customer_id),
                query: { tab: 'transactions' }
            })
            else if (route.query.redirect) router.push(route.query.redirect as string)
            else router.push('/app/transactions')
        })
    } catch (error) {
        const errors = catchFieldError(error);
        store.setFormErrors(errors);
        swal.fire({
            icon: 'error',
            title: 'Hata',
            text: 'İşlem kaydedilirken bir hata oluştu'
        })
    }
}

const resetOrderBased = async () => {
    orderBasedTransaction.value = false
    availableOrders.value = null
    store.transactionForm.customer_order_id = null
    $('#orderBased').prop('checked', false)
}

const getDataForOrderBased = async () => {
    if (!store.transactionForm.currency || !store.transactionForm.company_customer_id) {
        await resetOrderBased()
        return swal.fire({
            icon: 'error',
            title: 'Hata',
            text: 'Önce müşteri ve kur seçiniz'
        })
    }
    try {
        viewWrapper.setLoading(true)
        const { data } = await api.getFormInputData({
            currency: store.transactionForm.currency,
            company_customer_id: store.transactionForm.company_customer_id,
        })
        if (data.data.customer_orders.length < 1) {
            await resetOrderBased()
            return swal.fire({
                icon: 'error',
                title: 'Hata',
                text: 'Seçilen müşteri ve kur için sipariş bulunamadı. Bu siparişe göre hareket işlemi yapılamaz. Sipariş veya kurlar uyuşmuyor/sipariş bulunamıyor olabilir.'
            })
        } else {
            availableOrders.value = data.data.customer_orders
        }
    } catch (error) {
        catchFieldError(error)
        await resetOrderBased()
    } finally {
        viewWrapper.setLoading(false)
    }
}

//order based transaction watcher
watch(orderBasedTransaction, async (value) => {
    if (value) {
        await getDataForOrderBased()
    } else {
        await resetOrderBased()
    }
}, { immediate: true })

watch([() => store.transactionForm.currency, () => store.transactionForm.company_customer_id], () => {
    resetOrderBased()
})

watch(() => store.transactionForm.customer_order_id, () => {
    if (store.transactionForm.customer_order_id) {
        store.transactionForm.amount = availableOrders.value?.find(order => order.id === store.transactionForm.customer_order_id)?.grand_total || 0
    }
})

onMounted(async () => {
    await getCustomers()
    if (route_customer_id) {
        store.transactionForm.company_customer_id = route_customer_id
    }
})

onBeforeUnmount(() => {
    store.$reset();
    store.$dispose();
})
</script>
<template>
    <div class="card">
        <form @submit="saveTransaction">
            <div class="card-body">
                <VAlert type="alert-info">
                    Hareket kaydı oluşturduktan sonra hareket içeriğini detaylandırabilirsiniz.
                </VAlert>

                <div class="row">
                    <div class="col-md-6">
                        <!-- fiche no -->

                        <InputWithError :errors="store.formErrors.fiche_no">
                            <label for="fiche_no">Fiş No</label>
                            <input :value="store.transactionForm.fiche_no" type="text" class="form-control" disabled
                                id="fiche_no">
                        </InputWithError>

                        <!-- customer list -->
                        <InputWithError :errors="store.formErrors.company_customer_id">
                            <label for="customer">Müşteri</label>
                            <select v-model="store.transactionForm.company_customer_id" class="form-control"
                                :disabled="hasCompanyCustomer" id="customer">
                                <option value="">Seçiniz</option>
                                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                    {{ customer.name }}
                                </option>
                            </select>
                        </InputWithError>


                        <InputWithError :errors="store.formErrors.date">

                            <label for="date">İşlem Tarihi</label>
                            <input v-model="store.transactionForm.date" type="date" class="form-control" id="date">

                        </InputWithError>

                        <!-- is_paid -->
                        <InputWithError v-if="paidSwitchActive" :errors="store.formErrors.is_paid">
                            <div class="d-flex flex-column">
                                <label for="is_paid">Ödeme Durumu</label>
                                <InputSwitch v-model="store.transactionForm.is_paid" id="is_paid" offLabel="Ödenmedi"
                                    onLabel="Ödendi" />
                            </div>

                        </InputWithError>
                    </div>
                    <div class="col-md-6">

                        <!-- fiche type -->

                        <InputWithError :errors="store.formErrors.fiche_type">
                            <label for="fiche_type">Fiş Tipi</label>
                            <select v-model="store.transactionForm.fiche_type" class="form-control" id="fiche_type">
                                <option value="">Seçiniz</option>
                                <option v-for="type in transactionList" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>
                        </InputWithError>

                        <VInputCurrencies v-model="store.transactionForm.currency" :errors="store.formErrors.currency"
                            label="Kur" />

                        <!-- due date -->
                        <InputWithError v-if="paidSwitchActive" :errors="store.formErrors.due_date">
                            <label for="due_date">Vade Tarihi</label>
                            <input v-model="store.transactionForm.due_date" type="date" class="form-control"
                                :min="store.transactionForm.date" id="due_date">
                        </InputWithError>


                        <!-- amount -->
                        <InputWithError :errors="store.formErrors.amount">
                            <label for="amount">Tutar</label>
                            <div class="input-group">
                                <input v-model="store.transactionForm.amount" type="number" class="form-control"
                                    step="0.01" min="0" placeholder="0.00" id="amount"
                                    :disabled="orderBasedTransaction">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        {{ getCurrencySymbol(store.transactionForm.currency as CurrencyTypes) }}
                                    </span>
                                </div>
                            </div>
                        </InputWithError>



                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 p-3">
                        <!-- açıklama -->
                        <InputWithError :errors="store.formErrors.description">
                            <label for="description">Açıklama</label>
                            <textarea v-model="store.transactionForm.description" class="form-control" id="description"
                                rows="3"></textarea>
                        </InputWithError>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-bayi-red" @click.prevent="saveTransaction">
                            Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped></style>
