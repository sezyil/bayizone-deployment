<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Seçenek Listesi');
const router = useRouter();
const permission = useUserPermission().getByName('option');
if (!permission.view && !permission.create) {
    await swalPermissionDenied(() => router.push('/app'));
}
</script>
<template>
    <PageContent>
        <template #header v-if="permission.create">
            <RouterLink to="/app/catalog/options/create" class="btn btn-bayi-red">
                <i class="fas fa-plus mr-2"></i> Yeni
            </RouterLink>
        </template>
        <OptionsIndex v-if="permission.view" :can-delete="permission.delete" :can-update="permission.update" />
    </PageContent>
</template>



<style scoped></style>