<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import { list as customerReq } from '/@src/request/request-customer';
import { requestCurrency } from '/@src/request/request-utilities';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { ICurrencyResponse } from '/@src/shared/response/interface-utils-response';
const router = useRouter();
const emit = defineEmits(['searchTriggered'])
const swal = useSwal()
export interface IOfferFilter {
    company_customer_id?: string
    customer_email?: string
    currency?: string
    dateFilterType?: string
    firstDate?: string
    secondDate?: string
}
const setDefault = (): IOfferFilter => ({
    company_customer_id: '',
    customer_email: '',
    currency: '',
    dateFilterType: '',
    firstDate: '',
    secondDate: ''
})
const filter = ref<IOfferFilter>(setDefault())



const sanitizedFilters = computed(() => {
    const _tmp: IOfferFilter = {}
    for (const key in filter.value) {
        if (filter.value[key as keyof IOfferFilter]) {
            _tmp[key as keyof IOfferFilter] = filter.value[key as keyof IOfferFilter]
        }
    }
    return _tmp
})

const customerData = ref<Array<{
    id: string
    name: string
}>>([])

const currencyData = ref<ICurrencyResponse[]>([])

const getCustomer = async () => {
    try {
        const { data } = await customerReq()
        customerData.value = data.data
    } catch (e) {
        swal.fire({
            icon: 'error',
            title: 'Hata',
            text: 'Müşteriler yüklenirken bir hata oluştu'
        })
    }
}

const getCurrencies = async () => {
    try {
        const { data } = await requestCurrency()
        currencyData.value = data.data as ICurrencyResponse[]

    } catch (e) { }
}
const clearAndSearch = () => {
    filter.value = setDefault()
    emitSearch()
}
const emitSearch = () => emit('searchTriggered', sanitizedFilters.value)


const getPageQueries = async () => {
    const queries = router.currentRoute.value.query;
    if (queries.customer_id) {
        //check if customer id is valid
        const customer = customerData.value.find(c => c.id === queries.customer_id)
        if (customer) filter.value.company_customer_id = queries.customer_id as string;
        //else alert('Müşteri bulunamadı')// TODO: show error


    }
}

watch(() => filter.value?.firstDate, (val) => {
    if (!val) return;
    filter.value.secondDate = ''
})
watch(() => filter.value?.dateFilterType, (val) => {
    if (!val) filter.value.firstDate = '', filter.value.secondDate = ''
})

onMounted(async () => {
    await getCustomer()
    await getCurrencies()
    await getPageQueries();

    emitSearch()
})

</script>

<template>
    <div class="card">
        <div class="card-body">
            <div class="row  mb-1">
                <div class="col-md-4">
                    <label for="customer_name">Müşteri Adı</label>
                    <select class="form-control" id="customer_name" v-model="filter.company_customer_id">
                        <option value="">Hepsi</option>
                        <option v-for="customer in customerData" :key="customer.id" :value="customer.id">
                            {{ customer.name }}
                        </option>
                    </select>
                </div>
                <!-- currency -->


                <div class="col-md-4">
                    <label for="customer_email">Email</label>
                    <input type="text" class="form-control" id="customer_email" v-model="filter.customer_email"
                        placeholder="Email">
                </div>

                <div class="col-md-4">
                    <label for="currency">Para Birimi</label>
                    <select class="form-control" id="currency" v-model="filter.currency">
                        <option value="">Hepsi</option>
                        <option v-for="currency in currencyData" :key="currency.code" :value="currency.code">
                            {{ currency.title }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="offer_create_date">Tarih Başlangıç</label>
                    <input type="date" class="form-control" id="offer_create_date" v-model="filter.firstDate">
                </div>
                <div class="col-md-4">
                    <label for="offer_due_date">Tarih Bitiş</label>
                    <input type="date" class="form-control" id="offer_due_date" :min="filter.firstDate"
                        v-model="filter.secondDate">
                </div>
            </div>


            <div class="row mt-2">
                <!-- search button left aligned -->
                <div class="col-md-12 d-flex justify-content-end">
                    <!-- clear -->
                    <VButtonClear @click="clearAndSearch" />
                    <VButtonSearch @click="emitSearch" />
                </div>
            </div>
        </div>
    </div>
</template>