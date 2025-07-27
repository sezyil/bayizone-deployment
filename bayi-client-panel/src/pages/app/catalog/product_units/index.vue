<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Ürün Birimleri');
const router = useRouter();
const permission = useUserPermission().getByName('unit');
if (!permission.view && !permission.create) {
    await swalPermissionDenied(() => router.push('/app'));
}
</script>
<template>
    <PageContent>
        <template #header v-if="permission.create">
            <VButtonCreate @click="$router.push('/app/catalog/product_units/create')" />
        </template>
        <ProductUnitIndex v-if="permission.view" :can-update="permission.update" :can-delete="permission.delete" />
    </PageContent>
</template>



<style scoped></style>