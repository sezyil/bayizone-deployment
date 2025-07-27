<script setup lang="ts">
import { PropType } from 'vue';
import { IDistrictResponse } from '/@src/shared/response/interface-utils-response';
import { requestCountry } from '/@src/request/request-utilities';
import { useI18n } from 'vue-i18n';

const emits = defineEmits(['update:modelValue']);
const { t } = useI18n();
const props = defineProps({
    errors: {
        type: Array as PropType<string[]>,
        default: () => []
    },
    modelValue: {
        type: Number,
        default: 0
    },
})
const icon = ref<string | undefined>(undefined);

const dataList = ref<{
    id: number;
    name: string;
}[]>([]);

const getData = async () => {
    const { data } = await requestCountry();
    dataList.value = data.data;
    //if modelValue is not in districts, set modelValue to 0
    if (props.modelValue !== 0 && !dataList.value.find(x => x.id === props.modelValue)) {
        emits('update:modelValue', 0)
    }

}

const change = (e: Event) => {
    const target = e.target as HTMLSelectElement;
    let value = Number(target.value);
    emits('update:modelValue', value)
}

onMounted(() => {
    getData();
})


</script>
<template>
    <InputWithError :errors="errors" :icon="icon" :type="'right-icon'" class="bz-i-district-w">
        <label>{{ t('common.country') }}</label>
        <select :value="modelValue" class="form-control" @change="change">
            <option value="0" disabled>{{ t('actions.select') }}</option>
            <option v-for="item in dataList" :key="item.id" :value="item.id">
                {{ item.name }}
            </option>
        </select>
    </InputWithError>
</template>