<script setup lang="ts">
import { IProductUnitResponse } from '/@src/shared/response/interface-utils-response';
import { list } from '/@src/request/request-product-unit';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useSwal } from '/@src/composables/useSwal';
const swal = useSwal();
const emits = defineEmits(['update:modelValue']);

const isLoading = ref<boolean>(false);
const props = defineProps({
    modelValue: {
        type: Number,
        default: 1
    },
    labelClass: {
        type: String,
        default: ''
    }
})
const icon = computed(() => {
    return isLoading.value ? 'fas fa-spinner fa-spin' : '';
})

const dataList = ref<IProductUnitResponse[]>([]);

const getData = async () => {
    try {
        isLoading.value = true;
        const { data } = await list();
        const response = data.data as IProductUnitResponse[];
        dataList.value = response;
    }
    catch (err) {
        swal.fire('Hata', 'Ürün birimleri alınamadı', 'error');
    }
    finally {
        isLoading.value = false;
    }
}


const change = (e: Event) => {
    const target = e.target as HTMLSelectElement;
    let value = target.value as unknown as number;
    emits('update:modelValue', Number(value))
}

onMounted(async () => {
    await getData();
})

</script>
<template>
    <select :value="modelValue" class="form-control" @change="change">
        <option value="0" disabled>Seçiniz</option>
        <option v-for="item in dataList" :key="item.id" :value="item.id">{{ item.name }}</option>
    </select>
</template>