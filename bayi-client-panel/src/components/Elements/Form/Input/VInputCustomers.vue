<script setup lang="ts">
import { PropType } from 'vue';
import { ICurrencyResponse, CurrencyTypes } from '/@src/shared/response/interface-utils-response';
import { list } from '/@src/request/request-customer';
import { SwalInstance } from '/@src/shared/common/type-swal';
const emits = defineEmits(['update:modelValue']);
interface IVInputCustomerList {
    id: string
    name: string
    phone: string
    email: string
    group: string
    code: string
    has_user: boolean
    country_name: string
    city: any
}
const customers = ref<IVInputCustomerList[]>([])
const isLoading = ref<boolean>(false);
const props = defineProps({
    errors: {
        type: Array as PropType<string[]>,
        default: () => []
    },
    modelValue: {
        type: String,
        default: ''
    }
})
const icon = computed(() => {
    return isLoading.value ? 'fas fa-spinner fa-spin' : '';
})

const getCustomers = async () => {
    isLoading.value = true;
    const { data } = await list();
    customers.value = data.data as IVInputCustomerList[];
    isLoading.value = false;
}




const change = (e: Event) => {
    const target = e.target as HTMLSelectElement;
    let value = target.value as CurrencyTypes;
    emits('update:modelValue', value)
}

onMounted(async () => {
    await getCustomers();
})

</script>
<template>
    <InputWithError :errors="errors" :icon="icon" :type="'right-icon'">
        <label>Müşteri</label>
        <select :value="modelValue" @change="change" class="form-control" :disabled="isLoading">
            <option value="">Seçiniz</option>
            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                {{ customer.name }}
            </option>
        </select>
    </InputWithError>
</template>