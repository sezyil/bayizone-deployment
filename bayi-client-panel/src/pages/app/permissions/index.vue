<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const router = useRouter();
const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle('Grup Yetkileri');
const permission = useUserPermission().getByName('permission')
if (!permission.view) {
    swalPermissionDenied(() => router.push('/app'))
}
</script>
<template>
    <PageContent>
        <PermissionIndex v-if="permission.view" :can-update="permission.update" />
    </PageContent>
</template>



<style scoped></style>