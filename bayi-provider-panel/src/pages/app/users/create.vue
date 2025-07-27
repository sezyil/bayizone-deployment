<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useViewWrapper } from '/@src/stores/viewWrapper';
const { t } = useI18n();
const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle(t('components.users.create.title'));

</script>
<template>
    <PageContent>
        <UserForm />
    </PageContent>
</template>

<style scoped></style>