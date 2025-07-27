<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Birim Ekle');
const router = useRouter();
const permission = useUserPermission().getByName('unit');
if (!permission.create) {
    await swalPermissionDenied(() => router.push('/app'));
}
</script>
<template>
    <PageContent>
        <ProductUnitForm v-if="permission.create" />
    </PageContent>
</template>



<style scoped></style>