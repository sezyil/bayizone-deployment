<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const { t } = useI18n();
const viewWrapper = useViewWrapper();
const router = useRouter();
const route = useRoute();

viewWrapper.setPageTitle(t('components.bank_accounts.edit.title'));
const { id } = route.params as { id: string, bank_account_id: string };

//if param id not string or empty redirect to user page
if (!id) router.push('/app/bank_accounts');
</script>

<template>
    <PageContent>
        <BankAccountForm :bank_account_id="id" />
    </PageContent>
</template>

<style lang="scss" scoped></style>