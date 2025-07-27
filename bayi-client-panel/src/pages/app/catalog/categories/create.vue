<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
const router = useRouter();
viewWrapper.setPageTitle('Kategori Ekle');
const permission = useUserPermission().getByName('category');
if (!permission.create) {
    await swalPermissionDenied(() => router.push('/app'));
}
</script>
<template>
    <PageContent>
        <CategoriesForm v-if="permission.create" />
    </PageContent>
</template>



<style scoped></style>