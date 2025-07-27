<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Varyant Değeri Ekle');
const router = useRouter();
const permission = useUserPermission().getByName('variant');
if (!permission.create) {
    await swalPermissionDenied(() => router.push('/app/catalog/variant_values'));
}
</script>
<template>
    <PageContent>
        <VariantValueForm v-if="permission.create" />
    </PageContent>
</template>



<style scoped></style>