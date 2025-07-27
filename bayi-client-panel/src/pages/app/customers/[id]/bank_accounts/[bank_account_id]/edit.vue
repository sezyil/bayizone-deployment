<script  setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();
const permission = useUserPermission().getByName('company_customer_bank_account');
if (!permission.update) {
    await swalPermissionDenied(() => router.push('/app'));
}

viewWrapper.setPageTitle('Müşteri Banka Bilgileri');
const { id: company_customer_id, bank_account_id } = route.params as { id: string, bank_account_id: string };

//if param id not string or empty redirect to user page
if (!company_customer_id || !bank_account_id) router.push('/app/customers/bank_accounts');
</script>

<template>
    <PageContent>
        <CustomerBankAccountForm :customer_id="company_customer_id" :bank_account_id="bank_account_id"
            v-if="permission.update" />
    </PageContent>
</template>

<style lang="scss" scoped></style>