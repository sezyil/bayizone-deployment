<script setup lang="ts">
import { PropType } from 'vue';
import { ICurrencyResponse, CurrencyTypes } from '/@src/shared/response/interface-utils-response';
import { requestCurrency } from '/@src/request/request-utilities';
import { useSwal } from '/@src/composables/useSwal';
const swal = useSwal();
const emits = defineEmits(['update:modelValue']);

const isLoading = ref<boolean>(false);
const props = defineProps({
    errors: {
        type: Array as PropType<string[]>,
        default: () => []
    },
    modelValue: {
        type: String as PropType<CurrencyTypes | "0">,
        default: 'tl'
    }
})
const icon = computed(() => {
    return isLoading.value ? 'fas fa-spinner fa-spin' : '';
})

const currencies = ref<ICurrencyResponse[]>([]);

const getCurrencies = async () => {
    try {
        isLoading.value = true;
        const { data } = await requestCurrency();
        const response = data.data as ICurrencyResponse[];
        currencies.value = response;
    }
    catch (err) {
        swal.fire('Hata', 'Para birimleri alınamadı', 'error');
    }
    finally {
        isLoading.value = false;
    }
}


const change = (e: Event) => {
    const target = e.target as HTMLSelectElement;
    let value = target.value as CurrencyTypes;
    emits('update:modelValue', value)
}

onMounted(async () => {
    await getCurrencies();
})

</script>
<template>
    <InputWithError :errors="errors" :icon="icon" :type="'right-icon'" class="bz-i-currency-w">
        <label>Para Birimi</label>
        <select :value="modelValue" class="form-control" @change="change">
            <option value="0">Seçiniz</option>
            <option v-for="currency in currencies" :key="currency.id" :value="currency.code">{{ currency.title }}
            </option>
        </select>
        <slot></slot>
    </InputWithError>
</template>