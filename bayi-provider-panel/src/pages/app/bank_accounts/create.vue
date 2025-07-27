<script setup lang="ts">
import { useI18n } from "vue-i18n";
import { useViewWrapper } from "/@src/stores/viewWrapper";
const { t } = useI18n();
const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle(t("components.bank_accounts.create.title"));
</script>

<template>
  <PageContent>
    <BankAccountForm />
  </PageContent>
</template>

<style lang="scss" scoped></style>
