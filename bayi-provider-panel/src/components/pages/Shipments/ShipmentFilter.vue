<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useSwal } from '/@src/composables/useSwal';
import { requestCurrency } from '/@src/request/request-utilities';
import { CustomerShipmentStatusEnum } from '/@src/shared/response/interface-shipment-response';
import { ICurrencyResponse } from '/@src/shared/response/interface-utils-response';
const emit = defineEmits(['searchTriggered'])
const { t } = useI18n()

export interface IShipmentFilter {
    shipment_no: string,
    status: string
}
const setDefault = (): IShipmentFilter => ({
    shipment_no: '',
    status: ''
})
const filter = ref<{
    shipment_no: string,
    status: string
}>(setDefault())

function customerShipmentStatusEnumDescription(status: CustomerShipmentStatusEnum): string {
    switch (status) {
        case CustomerShipmentStatusEnum.DRAFT:
            return t('statuses.common.draft');
        case CustomerShipmentStatusEnum.PENDING:
            return t('statuses.shipment.pending');
        case CustomerShipmentStatusEnum.SHIPPED:
            return t('statuses.shipment.shipped');
        case CustomerShipmentStatusEnum.DELIVERED:
            return t('statuses.shipment.delivered');
        case CustomerShipmentStatusEnum.CANCELED:
            return t('statuses.common.canceled');
        default:
            return t('statuses.common.unknown');
    }
}

const sanitizedFilters = computed(() => {
    const _tmp: IShipmentFilter = {
        shipment_no: '',
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

const availableStatuses = computed(() => {
    //except draft
    return Object.values(CustomerShipmentStatusEnum).filter((status) => status !== CustomerShipmentStatusEnum.DRAFT)
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
                    <div class="form-group">
                        <label for="shipment_no">{{ t('components.shipment.shipment_no') }}</label>
                        <input v-model="filter.shipment_no" type="text" class="form-control"
                            :placeholder="t('components.shipment.shipment_no')" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">{{ t('common.status') }}</label>
                        <select v-model="filter.status" class="form-control">
                            <option value="">{{ t('common.all') }}</option>
                            <option v-for="status in availableStatuses" :value="status">
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