<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Varyant Ekle');
const router = useRouter();
const permission = useUserPermission().getByName('variant');
if (!permission.create) {
    await swalPermissionDenied(() => router.push('/app/catalog/variants'));
}
</script>
<template>
    <PageContent>
        <VariantForm v-if="permission.create" />
    </PageContent>
</template>



<style scoped></style>