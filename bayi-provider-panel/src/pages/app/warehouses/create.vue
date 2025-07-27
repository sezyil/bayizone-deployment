<script setup lang="ts">
import { useI18n } from "vue-i18n";
import { useViewWrapper } from "/@src/stores/viewWrapper";
const { t } = useI18n();
const viewWrapper = useViewWrapper();

viewWrapper.setPageTitle(t("components.warehouses.create.title"));
</script>

<template>
  <PageContent>
    <WarehouseForm />
  </PageContent>
</template>

<style lang="scss" scoped></style>
