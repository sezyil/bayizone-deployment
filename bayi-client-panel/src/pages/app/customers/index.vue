<script setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';


const userSession = useUserSession();
const viewWrapper = useViewWrapper();
const router = useRouter();
viewWrapper.setPageTitle('Müşteriler');
const { getByName } = useUserPermission();
const permission = {
    company: getByName('company_customer'),
    warehouse: getByName('company_customer_warehouse'),
    bank_account: getByName('company_customer_bank_account'),
}
if (!permission.company.view && !permission.company.create) {
    await swalPermissionDenied(() => router.push('/app'));
}
</script>
<template>
    <PageContent>
        <template #header v-if="permission.company.create">
            <VButtonCreate @click="$router.push('/app/customers/create')" />
        </template>
        <CustomerIndex v-if="permission.company.view" :can-delete="permission.company.delete"
            :can-update="permission.company.update" :can-view-bank-account="permission.bank_account.view"
            :can-view-warehouse="permission.warehouse.view" />
    </PageContent>
</template>



<style scoped></style>