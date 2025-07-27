<script setup lang="ts">
import { PropType } from "vue";
import { requestCity } from "/@src/request/request-utilities";

const emits = defineEmits(["update:modelValue", "update:city_name"]);

const props = defineProps({
    errors: {
        type: Array as PropType<string[]>,
        default: () => [],
    },
    modelValue: {
        type: Number,
        default: 0,
    },
    state_id: {
        type: Number,
        default: 0,
    },
    withName: {
        type: Boolean,
        default: false,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: "İlçe",
    },
});
const icon = ref<string | undefined>(undefined);

const dataList = ref<
    {
        id: number;
        name: string;
    }[]
>([]);

const getData = async () => {
    let nullDatas = [0, undefined, null, ""]
    if (nullDatas.includes(props.state_id)) {
        emits('update:modelValue', 0);
        return dataList.value = [];
    }
    const { data } = await requestCity(props.state_id);
    dataList.value = data.data;
    //if modelValue is not in districts, set modelValue to 0
    if (
        props.modelValue !== 0 &&
        !dataList.value.find((x) => x.id === props.modelValue)
    ) {
        emits("update:modelValue", 0);
    }
};

watch(
    () => props.state_id,
    async () => {
        icon.value = "icon-spinner2 spinner";
        await getData();
        icon.value = undefined;
    }
);

const change = (e: Event) => {
    const target = e.target as HTMLSelectElement;
    let value = Number(target.value);
    emits("update:modelValue", value);
    if (props.withName) {
        emits("update:city_name", target.options[target.selectedIndex].text);
    }
};

onMounted(async () => {
    await getData();
});
</script>
<template>
    <InputWithError :errors="errors" :icon="icon" :type="'right-icon'" class="bz-i-cities-w">
        <label>{{ label }}</label>
        <select :value="modelValue" class="form-control" @change="change" :disabled="readonly">
            <option value="0">Seçiniz</option>
            <option v-for="item in dataList" :key="item.id" :value="item.id">
                {{ item.name }}
            </option>
        </select>
    </InputWithError>
</template>
