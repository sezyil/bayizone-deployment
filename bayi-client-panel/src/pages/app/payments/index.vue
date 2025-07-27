<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { list } from '/@src/request/request-customer';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const router = useRouter();
const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle('Ödemeler');
const permission = useUserPermission().getByName('payment');
onBeforeMount(async () => {
    if (!permission.view) {
        await swalPermissionDenied(() => router.push('/app'));
    }
});

</script>
<template>
    <PageContent>
        <PaymentIndex />
    </PageContent>
</template>



<style scoped></style>