<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();
const permission = useUserPermission().getByName('product');
viewWrapper.setPageTitle('Ürün Detayı');

const { id } = route.params as { id: string };
const product_id = Number(id);
if (!permission?.update) {
    await swalPermissionDenied(() => router.push('/app'));
}
//if param id not string or empty redirect to product page
if (isNaN(product_id)) router.push('/app/catalog/product');
const saveTriggered = ref(false);
const handleSave = () => {
    if (saveTriggered.value) return;
    saveTriggered.value = true;
};


</script>

<template>
    <PageContent>
        <template #header v-if="permission?.update">
            <VButtonSave @click="handleSave" :disabled="saveTriggered" />
        </template>
        <ProductForm :product_id="product_id" v-if="permission?.update" :save-triggered="saveTriggered"
            @save-triggered="saveTriggered = $event" />
    </PageContent>
</template>

<style lang="scss" scoped></style>