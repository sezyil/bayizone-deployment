<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Özellik Ekle');
const router = useRouter();
const permission = useUserPermission().getByName('option');
if (!permission.create) {
    await swalPermissionDenied(() => router.push('/app'));
}
</script>
<template>
    <PageContent>
        <OptionsForm v-if="permission.create" />
    </PageContent>
</template>



<style scoped></style>