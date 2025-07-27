<script setup lang="ts">
import { IVariantListItemTransformed } from './Variant/TVariantColor.vue';
import VariantsApi, { IVariantListItem } from '/@src/request/request-variant';
import { useCatalogProductStore } from '/@src/stores/catalog/product';
const api = new VariantsApi();
const variantList = ref<IVariantListItem[]>([]);

const store = useCatalogProductStore();
interface ITProductVariantGrouped {
    label: string;
    type: string;
    variants: IVariantListItem[];
}

const getVariants = async () => {
    try {
        const { data } = await api.listWithValues();
        variantList.value = data.data
    } catch (error) {
        console.log(error);

    }
}

const dimensionVariants = computed(() => {
    return variantList.value.filter(variant => variant.type === 'DIMENSION');
})

const colorVariants = computed(() => {
    return variantList.value.filter(variant => variant.type === 'COLOR') as IVariantListItemTransformed[]
})

onMounted(async () => {
    await getVariants();
})
</script>
<template>
    <div>
        <!-- color -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3>Ürün Renkleri</h3>
                    <button class="btn btn-sm btn-primary" @click.prevent="store.addVariant('COLOR')">Renk Ekle</button>
                </div>
            </div>
            <div class="card-body" v-if="store.productData.colors.length > 0">
                <TVariantColor v-for="(color, index) in store.productData.colors" :key="index" :existData="color"
                    :availableVariants="colorVariants" :index="index" />
            </div>
            <div class="card-body" v-else>
                <p class="text-center">Varyasyon Renk Bulunmamaktadır.</p>
            </div>
        </div>
        <!-- dimension -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3>Ürün Ölçüleri</h3>
                    <button class="btn btn-sm btn-primary" @click.prevent="store.addVariant('DIMENSION')">Ölçü
                        Ekle</button>
                </div>
            </div>
            <div class="card-body" v-if="store.productData.dimensions.length > 0">
                <TVariantDimension v-for="(dimension, index) in store.productData.dimensions" :key="index"
                    :availableVariants="dimensionVariants" :index="index" />
            </div>
            <div class="card-body" v-else>
                <p class="text-center">Varyasyon Ölçü Bulunmamaktadır.</p>
            </div>

        </div>
    </div>
</template>


<style scoped></style>