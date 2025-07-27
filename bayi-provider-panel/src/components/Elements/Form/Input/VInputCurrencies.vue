<script setup lang="ts">
import { PropType } from 'vue';
import { ICurrencyResponse, CurrencyTypes } from '/@src/shared/response/interface-utils-response';
import { requestCurrency } from '/@src/request/request-utilities';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useI18n } from 'vue-i18n';
const swal = inject('$swal') as SwalInstance;
const emits = defineEmits(['update:modelValue']);
const { t } = useI18n();
const isLoading = ref<boolean>(false);
const props = defineProps({
    errors: {
        type: Array as PropType<string[]>,
        default: () => []
    },
    modelValue: {
        type: String as PropType<CurrencyTypes>,
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
        <label>{{ t('common.currency') }}</label>
        <select :value="modelValue" class="form-control" @change="change">
            <option value="0" disabled>{{ t('actions.select') }}</option>
            <option v-for="currency in currencies" :key="currency.id" :value="currency.code">{{ currency.title }}
            </option>
        </select>
    </InputWithError>
</template>