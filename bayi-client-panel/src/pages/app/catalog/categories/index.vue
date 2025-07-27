<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Kategori Listesi');
const router = useRouter();
const permission = useUserPermission().getByName('category');
if (!permission.view && !permission.create) {
    await swalPermissionDenied(() => router.push('/app'));
}
</script>
<template>
    <PageContent>
        <template #header v-if="permission.create">
            <VButtonCreate @click="$router.push('/app/catalog/categories/create')" />
        </template>
        <CategoriesIndex v-if="permission.view" :can-delete="permission.delete" :can-update="permission.update" />
    </PageContent>
</template>



<style scoped></style>