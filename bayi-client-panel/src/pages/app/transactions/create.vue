<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const viewWrapper = useViewWrapper();
const permission = useUserPermission().getByName('transaction')
const router = useRouter();
viewWrapper.setPageTitle('Hareket Oluştur');

if (!permission.create) {
    await swalPermissionDenied(() => router.push('/app'))
}

</script>
<template>
    <PageContent>
        <TransactionForm v-if="permission.create" />
    </PageContent>
</template>

<style scoped></style>