<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const router = useRouter();
const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle('Teklif İstekleri');
const permission = useUserPermission().getByName('offer_request')
const offerPermission= useUserPermission().getByName('customer_offer')
if (!permission.view) {
    swalPermissionDenied(() => router.push('/app'))
}
</script>
<template>
    <PageContent>
        <OfferRequestIndex v-if="permission.view"  :can-create="offerPermission.create" />
    </PageContent>
</template>



<style scoped></style>