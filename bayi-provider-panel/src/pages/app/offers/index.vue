<script setup lang="ts">
import { useI18n } from "vue-i18n";
import { useViewWrapper } from "/@src/stores/viewWrapper";
const viewWrapper = useViewWrapper();
const { t } = useI18n();
viewWrapper.setPageTitle(t('components.offers.title'));
</script>
<template>
    <PageContent>
        <OfferIndex />
    </PageContent>
</template>

<style scoped></style>
