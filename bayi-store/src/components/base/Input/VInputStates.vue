<script setup lang="ts">
import { PropType } from 'vue';
import ApiUtilies from '/@src/request/api-utilities';
import { useI18n } from 'vue-i18n';
const api = new ApiUtilies();
const { t } = useI18n();
const emits = defineEmits(['update:modelValue', 'update:state_name']);
const attrs = useAttrs()
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
        const { data } = await api.requestState(props.country_id);
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
const computedLabel = computed(() => {
    const title: string = (attrs.title ?? "---") as string;
    return title;
});

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
    <div v-bind="$attrs">
        <VInputErrors :errors="errors" :label="computedLabel" class="flex flex-col space-y-1">
            <select id="states" @change="change"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="0">{{ t('form.select') }}</option>
                <option v-for="item in dataList" :key="item.id" :value="item.id">
                    {{ item.name }}
                </option>
            </select>
        </VInputErrors>
    </div>
</template>