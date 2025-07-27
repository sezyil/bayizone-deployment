<script setup lang="ts">
import { swalPermissionDenied, useSwal } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { list } from '/@src/request/request-customer';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const swal = useSwal();
const router = useRouter();
const viewWrapper = useViewWrapper();
const userStore = useUserSession();

viewWrapper.setPageTitle('Sevkler');
const permission = useUserPermission().getByName('customer_order');
onBeforeMount(async () => {
    if (!permission.view) {
        await swalPermissionDenied(() => router.push('/app'));
    }
});

const errorHandler = (title: string, text: string, route: string, buttonLabel: string = '') => {
    let confirmButtonText = buttonLabel?.length > 0 ? buttonLabel : 'Sayfaya Git';
    swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: false,
        confirmButtonText: confirmButtonText,
    }).then((result) => {
        router.push(route);
    });
}

</script>
<template>
    <PageContent>
        <template #header v-if="permission.view">
            <VButtonCreate @click="$router.push('/app/shipments/create')" />
        </template>
        <ShipmentIndex v-if="permission.view" />
    </PageContent>
</template>



<style scoped></style>