<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
import { list as customerReq } from '/@src/request/request-customer';
import { requestCurrency } from '/@src/request/request-utilities';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { CustomerShipmentStatusEnum, customerShipmentStatusEnumDescription } from '/@src/shared/response/interface-shipment-response';
import { ICurrencyResponse } from '/@src/shared/response/interface-utils-response';
const router = useRouter();
const emit = defineEmits(['searchTriggered'])
const swal = useSwal()

export interface IShipmentFilter {
    shipment_no: string,
    customer_name: string,
    status: string
}
const setDefault = (): IShipmentFilter => ({
    shipment_no: '',
    customer_name: '',
    status: ''
})
const filter = ref<{
    shipment_no: string,
    customer_name: string,
    status: string
}>(setDefault())



const sanitizedFilters = computed(() => {
    const _tmp: IShipmentFilter = {
        shipment_no: '',
        customer_name: '',
        status: ''
    }
    for (const key in filter.value) {
        if (filter.value[key as keyof IShipmentFilter]) {
            _tmp[key as keyof IShipmentFilter] = filter.value[key as keyof IShipmentFilter]
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
                    <div class="form-group">
                        <label for="shipment_no">Sevkiyat No</label>
                        <input v-model="filter.shipment_no" type="text" class="form-control"
                            placeholder="Sevkiyat No" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="customer_name">Müşteri Adı</label>
                        <input v-model="filter.customer_name" type="text" class="form-control"
                            placeholder="Müşteri Adı" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Durum</label>
                        <select v-model="filter.status" class="form-control">
                            <option value="">Tümü</option>
                            <option v-for="status in Object.values(CustomerShipmentStatusEnum)" :value="status">
                                {{ customerShipmentStatusEnumDescription(status) }}
                            </option>
                        </select>
                    </div>
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