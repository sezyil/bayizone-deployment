<script setup lang="ts">
import { PropType } from 'vue';
import { IProvinceResponse } from '/@src/shared/response/interface-utils-response';
import { requestProvince, requestState } from '/@src/request/request-utilities';

const emits = defineEmits(['update:modelValue', 'update:state_name']);

const props = defineProps({
    errors: {
        type: Array as PropType<string[]>,
        default: () => []
    },
    modelValue: {
        type: Number,
        default: 0
    },
    country_id: {
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
        default: 'İl'
    }
})
const icon = ref<string | undefined>(undefined);

const dataList = ref<{
    id: number;
    name: string;
}[]>([]);

const getData = async () => {
    let nullDatas = [0, undefined, null, ""]
    if (nullDatas.includes(props.country_id)) {
        emits('update:modelValue', 0);
        return dataList.value = [];
    }
    try {
        const { data } = await requestState(props.country_id);
        dataList.value = data.data;
        //if modelValue is not in provinces, set modelValue to 0
        if (props.modelValue !== 0 && !dataList.value.find(x => x.id === props.modelValue)) {
            emits('update:modelValue', 0)
        }
    }
    catch (err) {
        dataList.value = [];
    }
    finally {
        icon.value = undefined;
    }
}

watch(() => props.country_id, async () => {
    icon.value = "icon-spinner2 spinner";
    await getData();
    icon.value = undefined;
})

const change = (e: Event) => {
    const target = e.target as HTMLSelectElement;
    let value = Number(target.value);
    emits('update:modelValue', value)
    if (props.withName) {
        let name = target.options[target.selectedIndex].text;
        emits('update:state_name', name);
    }
}

onMounted(async () => {
    await getData();
})


</script>
<template>
    <InputWithError :errors="errors" :icon="icon" :type="'right-icon'" class="bz-i-province-w">
        <label>{{ label }}</label>
        <select :value="modelValue" class="form-control" @change="change" :disabled="readonly">
            <option value="0">Seçiniz</option>
            <option v-for="item in dataList" :key="item.id" :value="item.id">
                {{ item.name }}
            </option>
        </select>
    </InputWithError>
</template>