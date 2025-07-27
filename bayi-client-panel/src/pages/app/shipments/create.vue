<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import CustomerOfferApi from '/@src/request/request-customer-offer';
import { SwalInstance } from '/@src/shared/common/type-swal';
import { useViewWrapper } from '/@src/stores/viewWrapper';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const viewWrapper = useViewWrapper();
const triggerBtn = ref<HTMLFormElement>();
const api = new CustomerOfferApi();
const router = useRouter();
viewWrapper.setPageTitle('Sevk Oluştur');

const permission = useUserPermission().getByName('customer_order');
if (!permission.create) {
    await swalPermissionDenied(() => router.push('/app'));
}



</script>
<template>
    <PageContent>
        <form id="shipment_form">
            <ShipmentForm />
        </form>
    </PageContent>
</template>

<style scoped></style>