<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const router = useRouter();
const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle('Hareketler');
const permission = useUserPermission().getByName('transaction');
if (!permission.view) {
    swalPermissionDenied(() => router.push('/app'))
}
</script>
<template>
    <PageContent>
        <template #header v-if="permission.create">
            <VButtonCreate @click="$router.push('/app/transactions/create')" />
        </template>
        <TransactionIndex v-if="permission.view" />
    </PageContent>
</template>



<style scoped></style>