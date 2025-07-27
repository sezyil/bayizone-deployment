<script setup lang="ts">
import { useI18n } from "vue-i18n";
import { useViewWrapper } from "/@src/stores/viewWrapper";
const { t } = useI18n();
const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle(t("components.bank_accounts.index.title"));
</script>
<template>
  <PageContent>
    <template #header>
      <RouterLink :to="`/app/bank_accounts/create`" class="btn btn-indigo">
        <i class="fas fa-plus mr-2"></i> {{ t("common.new") }}
      </RouterLink>
    </template>
    <BankAccountIndex />
  </PageContent>
</template>

<style scoped></style>
