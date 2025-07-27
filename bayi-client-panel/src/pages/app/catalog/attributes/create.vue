<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Özellik Ekle');

const router = useRouter();
const permission = useUserPermission().getByName('attribute');
if (!permission?.create) {
    swalPermissionDenied(() => router.push('/app'));
}
</script>
<template>
    <PageContent>
        <AttributeForm v-if="permission?.create" />
    </PageContent>
</template>



<style scoped></style>