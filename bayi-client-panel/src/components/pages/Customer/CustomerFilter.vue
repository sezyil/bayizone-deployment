<script setup lang="ts">
import { useSwal } from '/@src/composables/useSwal';
const router = useRouter();
const emit = defineEmits(['searchTriggered'])
const swal = useSwal();
export interface ICustomerFilter {
    customer_name?: string
    customer_email?: string
    country?: number

}
const setDefault = (): ICustomerFilter => ({
    customer_name: '',
    customer_email: '',
    country: 0
})
const filter = ref<ICustomerFilter>(setDefault())



const sanitizedFilters = computed(() => {
    const _tmp: ICustomerFilter = {}
    for (const key in filter.value) {
        if (filter.value[key as keyof ICustomerFilter]) {
            /* @ts-ignore */
            _tmp[key as keyof ICustomerFilter] = filter.value[key as keyof ICustomerFilter]
        }
    }
    return _tmp
})

const customerData = ref<Array<{
    id: string
    name: string
}>>([])


const clearAndSearch = () => {
    filter.value = setDefault()
    emitSearch()
}
const emitSearch = () => emit('searchTriggered', sanitizedFilters.value)
</script>

<template>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label for="customer_name">Müşteri Adı</label>
                    <input type="text" class="form-control" id="customer_name" v-model="filter.customer_name"
                        placeholder="Müşteri Adı">
                </div>
                <div class="col-md-4">
                    <label for="customer_email">Email</label>
                    <input type="text" class="form-control" id="customer_email" v-model="filter.customer_email"
                        placeholder="Email">
                </div>
                <div class="col-md-4">
                    <VInputCountries v-model="filter.country" label="Ülke" />
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