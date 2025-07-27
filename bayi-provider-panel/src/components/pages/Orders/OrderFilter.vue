<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { requestCurrency } from '/@src/request/request-utilities';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { ICurrencyResponse } from '/@src/shared/response/interface-utils-response';
const { t } = useI18n();
const router = useRouter();
const emit = defineEmits(['searchTriggered'])
const swal = inject('$swal') as SwalInstance
export interface IOfferFilter {
    currency?: string
    dateFilterType?: string
    firstDate?: string
    secondDate?: string
}
const setDefault = (): IOfferFilter => ({
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

const currencyData = ref<ICurrencyResponse[]>([])


const getCurrencies = async () => {
    try {
        const { data } = await requestCurrency()
        currencyData.value = data.data as ICurrencyResponse[]

    } catch (e) { }
}

const emitSearch = () => emit('searchTriggered', sanitizedFilters.value)

watch(() => filter.value?.firstDate, (val) => {
    if (!val) return;
    filter.value.secondDate = ''
})
watch(() => filter.value?.dateFilterType, (val) => {
    if (!val) filter.value.firstDate = '', filter.value.secondDate = ''
})

onMounted(async () => {
    await getCurrencies()

    emitSearch()
})

</script>

<template>
    <div class="card">
        <div class="card-body">
            <div class="row mb-1">
                <div class="col-md-4">
                    <!-- due date or create date selectbox -->
                    <label for="dateFilterType">
                        {{ t('common.filter.dateType') }}
                    </label>
                    <select class="form-control" id="dateFilterType" v-model="filter.dateFilterType">
                        <option value="">{{ t('actions.select') }}</option>
                        <option value="create_date">{{ t('common.filter.createDate') }}</option>
                        <option value="order_date">{{ t('components.orders.filter.orderDate') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="offer_create_date">{{ t('common.filter.createDate') }}</label>
                    <input type="date" class="form-control" id="create_date" v-model="filter.firstDate">
                </div>
                <div class="col-md-4">
                    <label for="offer_due_date">{{ t('common.filter.dateEnd') }}</label>
                    <input type="date" class="form-control" id="order_date" :min="filter.firstDate"
                        v-model="filter.secondDate">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="currency">{{ t('common.currency') }}</label>
                    <select class="form-control" id="currency" v-model="filter.currency">
                        <option value="">{{ t('common.all') }}</option>
                        <option v-for="currency in currencyData" :key="currency.code" :value="currency.code">
                            {{ currency.title }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="row mt-2">
                <!-- search button left aligned -->
                <div class="col-md-12 d-flex justify-content-end">
                    <!-- clear -->
                    <button class="btn btn-outline-secondary mr-1" @click="filter = setDefault()"><i
                            class="fas fa-times mr-1"></i>{{ t('actions.clear') }}</button>
                    <button class="btn btn-primary" @click="emitSearch"><i class="fas fa-search mr-1"></i>
                        {{ t('actions.search') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>