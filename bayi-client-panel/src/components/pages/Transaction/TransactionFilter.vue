<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import { list as customerReq } from '/@src/request/request-customer';
import { requestCurrency } from '/@src/request/request-utilities';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { ICurrencyResponse } from '/@src/shared/response/interface-utils-response';
const router = useRouter();
const emit = defineEmits<{
    (e: 'searchTriggered', filter: ITransactionFilter): void
    (e: 'excelDownload', filter: ITransactionFilter): void
}>()
const swal = useSwal();
const props = defineProps({
    company_customer_id: {
        type: String,
        required: false
    },
    currency: {
        type: String,
        required: false
    },
    excelDownloading: {
        type: Boolean,
        required: false
    }
})
export interface ITransactionFilter {
    company_customer_id?: string
    customer_email?: string
    currency?: string
    io_type?: string
    due_status?: string
    start_date?: string
    end_date?: string
}
const setDefault = (): ITransactionFilter => ({
    company_customer_id: props.company_customer_id ? props.company_customer_id : '',
    customer_email: '',
    currency: props.currency ? props.currency : '',
    io_type: '',
    due_status: ''


})
const filter = ref<ITransactionFilter>(setDefault())

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
const emitSearch = () => emit('searchTriggered', filter.value)
const excelDownloadActive = computed(() => filter.value.currency)


const getPageQueries = async () => {
    const queries = router.currentRoute.value.query;
    if (queries.customer_id) {
        //check if customer id is valid
        const customer = customerData.value.find(c => c.id === queries.customer_id)
        if (customer) filter.value.company_customer_id = queries.customer_id as string;
        //else alert('Müşteri bulunamadı')// TODO: show error


    }
}

const handleHasProps = () => {
    if (props.company_customer_id) {
        filter.value.company_customer_id = props.company_customer_id
    }
    if (props.currency) {
        filter.value.currency = props.currency
    }
}

//currency or company_customer_id props can be changed
watch(() => [props.currency, props.company_customer_id], () => {
    handleHasProps()
    emitSearch()
})

const handleExcelDownload = () => {
    if (!filter.value.currency) {
        swal.fire({
            icon: 'error',
            title: 'Hata',
            text: 'Para birimi seçilmeden excel indirilemez'
        })
        return
    }
    emit('excelDownload', filter.value)
}

//end


onMounted(async () => {
    await getCustomer()
    await getCurrencies()
    if (!props.company_customer_id) {
        await getPageQueries();
    } else {
        handleHasProps()
    }

    emitSearch()
})

</script>

<template>
    <VInlineFilter @search="emitSearch" @clear="clearAndSearch">
        <template v-slot:beforeSearch>
            <button v-if="excelDownloadActive" class="btn btn-outline-success mr-1" :disabled="excelDownloading"
                @click.prevent="handleExcelDownload">
                <i class="fas fa-file-excel" v-if="!excelDownloading"></i>
                <i class="fas fa-spinner spinner" v-else></i>
                <span v-if="!excelDownloading" class="ml-1">Excel İndir</span>
                <span v-else class="ml-1">İndiriliyor...</span>
            </button>
        </template>

        <div class="row">
            <div class="col-md-4" v-if="!company_customer_id">
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
                <label for="start_date">İşlem Başlangıç Tarihi</label>
                <input type="date" class="form-control" id="start_date" :max="filter.end_date"
                    v-model="filter.start_date">
            </div>
            <div class="col-md-4">
                <label for="end_date">İşlem Bitiş Tarihi</label>
                <input type="date" class="form-control" :min="filter.start_date" id="end_date"
                    v-model="filter.end_date">
            </div>

            <div class="col-md-4">
                <label for="io_type">Hareket Tipi</label>
                <select class="form-control" id="io_type" v-model="filter.io_type">
                    <option value="">Hepsi</option>
                    <option value="debt">Borç</option>
                    <option value="credit">Alacak</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="due_status">Vade Durumu</label>
                <select class="form-control" id="due_status" v-model="filter.due_status">
                    <option value="">Hepsi</option>
                    <option value="due">Vadesi Geçenler</option>
                    <option value="not_due">Vadesi Geçmeyenler</option>
                </select>
            </div>

            <div class="col-md-4" v-if="!currency">
                <label for="currency">Para Birimi</label>
                <select class="form-control" id="currency" v-model="filter.currency">
                    <option value="">Hepsi</option>
                    <option v-for="currency in currencyData" :key="currency.code" :value="currency.code">
                        {{ currency.title }}
                    </option>
                </select>
            </div>
        </div>
    </VInlineFilter>
</template>