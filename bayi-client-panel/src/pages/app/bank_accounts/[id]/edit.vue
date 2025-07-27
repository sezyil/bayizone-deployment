<script  setup lang="ts">
import { swalPermissionDenied } from '/@src/composables/useSwal';
import { useUserPermission } from '/@src/composables/useUserPermission';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();

viewWrapper.setPageTitle('Banka Hesabı Düzenle');
const permission = useUserPermission().getByName('customer_bank_account');
if (!permission.update) {
    await swalPermissionDenied(() => router.push('/app'));
}
const { id: bank_account_id } = route.params as { id: string };

//if param id not string or empty redirect to user page
if (!bank_account_id) router.push('/app/users');
</script>

<template>
    <PageContent>
        <BankAccountsForm :bank_account_id="bank_account_id" v-if="permission.update" />
    </PageContent>
</template>

<style lang="scss" scoped></style>