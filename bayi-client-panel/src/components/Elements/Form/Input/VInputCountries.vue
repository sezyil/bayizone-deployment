<script setup lang="ts">
import { PropType } from 'vue';
import { IDistrictResponse } from '/@src/shared/response/interface-utils-response';
import { requestCountry } from '/@src/request/request-utilities';

const emits = defineEmits(['update:modelValue', 'update:country_name']);

const props = defineProps({
    errors: {
        type: Array as PropType<string[]>,
        default: () => []
    },
    modelValue: {
        type: Number,
        default: 0
    },
    withName: {
        type: Boolean,
        default: false
    },
    readonly: {
        type: Boolean,
        default: false
    },
    label: {
        type: String,
        default: 'Ülke'
    }
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
    if (props.withName) {
        emits('update:country_name', target.options[target.selectedIndex].text);
    }
}

onMounted(() => {
    getData();
})


</script>
<template>
    <InputWithError :errors="errors" :icon="icon" :type="'right-icon'" class="bz-i-district-w">
        <label>{{ label }}</label>
        <select :value="modelValue" class="form-control" @change="change" :disabled="readonly">
            <option value="0">Seçiniz</option>
            <option v-for="item in dataList" :key="item.id" :value="item.id">
                {{ item.name }}
            </option>
        </select>
    </InputWithError>
</template>