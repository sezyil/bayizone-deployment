<script setup lang="ts">
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle('Dashboard');
const userSession = useUserSession();
const wizardStatus = computed(() => userSession.wizardStatus);
const wizardFinished = computed(() => userSession.wizardFinished);
</script>
<template>
  <PageContent>
    <WizardStatus v-if="!wizardFinished" :has-address="wizardStatus.companyInfo" :has-image="wizardStatus.hasImage" />
    <IndexPage v-else />
  </PageContent>
</template>

<style scoped></style>