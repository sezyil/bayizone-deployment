<script setup lang="ts">
import { useUserSession } from '/@src/stores/userSession';
import { useViewWrapper } from '/@src/stores/viewWrapper';

const viewWrapper = useViewWrapper();
viewWrapper.setPageTitle('Dashboard');
const userSession = useUserSession();
</script>
<template>
  <PageContent>
    <IndexPage />
  </PageContent>
</template>

<style scoped></style>