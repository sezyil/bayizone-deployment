<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const { t } = useI18n();
const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle(t('account.settings'));
</script>
<template>
    <PageContent>
        <ProfileIndex />
    </PageContent>
</template>



<style scoped></style>